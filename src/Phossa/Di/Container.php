<?php
/**
 * Phossa Project
 *
 * PHP version 5.4
 *
 * @category  Library
 * @package   Phossa\Di
 * @author    Hong Zhang <phossa@126.com>
 * @copyright 2015 phossa.com
 * @license   http://mit-license.org/ MIT License
 * @link      http://www.phossa.com/
 */
/*# declare(strict_types=1); */

namespace Phossa\Di;

use Phossa\Di\Message\Message;
use Phossa\Di\Exception\LogicException;
use Phossa\Di\Exception\NotFoundException;

/**
 * Container
 *
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.1
 * @since   1.0.1 added
 */
class Container implements ContainerInterface
{
    use Definition\ResolvableTrait;

    /**
     * services pool
     *
     * @var    object[]
     * @access protected
     */
    protected $pool  = [];

    /**
     * circular detection for get()
     *
     * @var    array
     * @access protected
     */
    protected $circular = [];

    /**
     * Constructor
     *
     * Inject definitions or custom extensions
     *
     * @param  array|string $definitions array or filename
     * @param  array $providers provider objects or classnames
     * @param  Extension\ExtensionInterface[] $extensions
     * @throws LogicException if something goes wrong
     * @access public
     */
    public function __construct(
        $definitions = '',
        array $providers = [],
        array $extensions = []
    ) {
        // load extensions
        foreach ($extensions as $extension) {
            $this->addExtension($extension);
        }

        // load definitions
        if (!empty($definitions)) {
            $this->load($definitions);
        }

        // add service providers
        if (count($providers)) {
            foreach($providers as $provider) {
                $this->addProvider($provider);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        static $count = 0;

        // found service
        if ($this->has($id)) {
            /*
             * parameter 2 is the optional arguments array
             */
            $args  = func_num_args() > 1 ? func_get_arg(1) : [];

            /*
             * parameter 3 is the optional dynamic scope
             */
            $scope = func_num_args() > 2 ?
                (string) func_get_arg(2) :
                $this->getScope($id);

            // new id
            $newId = $scope . ':::' . $id;

            // try getting from the pool
            if (empty($args) && isset($this->pool[$newId])) {
                return $this->pool[$newId];
            }

            // circular detection
            if (isset($this->circular[$id])) {
                throw new LogicException(
                    Message::get(Message::SERVICE_CIRCULAR, $id),
                    Message::SERVICE_CIRCULAR
                );
            } else {
                // set circular mark
                $this->circular[$id] = ++$count;

                // unique scope, mark this object is shared under parent object
                if (isset($this->circular[$scope])) {
                    $scope += '::' . $count;
                }
            }

            // create the service
            $service = $this->createService($id, $args);

            // decorate the service
            $this->decorateService($service);

            // remove circular mark
            unset($this->circular[$id]);

            // store it except for the single scope
            if (self::SCOPE_SINGLE !== $scope) {
                $this->pool[$newId] = $service;
            }

            return $service;
        }

        // not found
        throw new NotFoundException(
            Message::get(Message::SERVICE_ID_NOT_FOUND, $id),
            Message::SERVICE_ID_NOT_FOUND
        );
    }

    /**
     * {@inheritDoc}
     */
    public function has($id)
    {
        // $id MUST BE string
        if (!is_string($id)) {
            return false;
        }

        // found in service definitions
        if (isset($this->services[$id])) {
            return true;
        }

        // found in one of the service providers
        if ($this->hasInProvider($id)) {
            return true;
        }

        // autowiring
        if ($this->autoWiringId($id)) {
            return true;
        }

        // not found
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function one(/*# string */ $id, array $arguments = [])
    {
        // force get a new instance
        return $this->get($id, $arguments, self::SCOPE_SINGLE);
    }

    /**
     * {@inheritDoc}
     */
    public function run($callable, array $arguments = [])
    {
        // resolve fake callable
        $call = $this->resolveCallable($callable);

        // execute the callable
        return call_user_func_array(
            $call,
            $this->resolveCallableArguments($call, $arguments)
        );
    }
}
