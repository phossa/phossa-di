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
    use Extension\ExtensibleTrait;

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
     * Inject definitions and providers
     *
     * @param  array|string $definitions array or filename
     * @param  array $providers provider objects or classnames
     * @throws LogicException if something goes wrong
     * @access public
     */
    public function __construct($definitions = '', array $providers = [])
    {
        // load definitions
        if (!empty($definitions)) {
            $this->load($definitions);
        }

        // add definition providers
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
        if ($this->has($id)) {
            list($args, $scope) = $this->fixGetArguments($id, func_get_args());

            // generate a local new id
            $newId = $scope . ':::' . $id;

            // try getting from the pool
            if (empty($args) && isset($this->pool[$newId])) {
                return $this->pool[$newId];
            }

            // check circular
            $this->checkCircular($id, $scope);

            // create the service
            $service = $this->createService($id, $args);

            // decorate the service
            $this->decorateService($service);

            // remove circular mark
            $this->removeCircular($id);

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
        if (is_string($id) &&
            (isset($this->services[$id])  ||
                $this->hasInProvider($id) ||
                $this->autoWiringId($id)
            )
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function one(/*# string */ $id, array $arguments = [])
    {
        return $this->get($id, $arguments, self::SCOPE_SINGLE);
    }

    /**
     * {@inheritDoc}
     */
    public function run($callable, array $arguments = [])
    {
        return $this->executeCallable($callable, $arguments);
    }

    /**
     * Parse/fix extra arguments for get()
     *
     * @param  string $id service id
     * @param  array $arguments get()'s argument array
     * @return array [$arguments, $scope]
     * @access protected
     */
    protected function fixGetArguments(
        /*# string */ $id,
        array $arguments
    )/*# : array */ {
        // parameter 2 is the optional arguments
        $args = isset($arguments[1]) ? (array) $arguments[1] : [];

        // parameter 3 is the optional scope
        $scope = isset($arguments[2]) ? (string) $arguments[2] :
                $this->getScope($id);

        return [ $args, $scope ];
    }

    /**
     * Check circular for get()
     *
     * @param  string $id service id
     * @param  string &$scope scope
     * @return void
     * @throws LogicException if circular found
     * @access protected
     */
    protected function checkCircular(/*# string */ $id, /*# string */ &$scope)
    {
        static $count = 0;

        // reference id "@$id@"
        $refId = $this->getReferenceId($id);

        // circular detection
        if (isset($this->circular[$refId])) {
            throw new LogicException(
                Message::get(Message::SERVICE_CIRCULAR, $id),
                Message::SERVICE_CIRCULAR
            );
        } else {
            $this->circular[$refId] = ++$count;

            // mark this object is shared under parent object
            if (isset($this->circular[$scope])) {
                $scope += '::' . $count;
            }
        }
    }

    /**
     * Remove circular mark for get()
     *
     * @param  string $id service id
     * @return void
     * @access protected
     */
    protected function removeCircular(/*# string */ $id)
    {
        unset($this->circular[$this->getReferenceId($id)]);
    }
}
