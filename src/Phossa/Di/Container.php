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
 * @see     ContainerInterface
 * @version 1.0.4
 * @since   1.0.1 added
 */
class Container implements ContainerInterface
{
    use Factory\GetServiceTrait;

    /**
     * services pool
     *
     * @var    object[]
     * @access protected
     */
    protected $pool = [];

    /**
     * Constructor
     *
     * Inject definitions and providers
     *
     * @param  array|string $definitions array or a filename
     * @param  array $providers provider objects or classnames
     * @throws LogicException if something goes wrong
     * @access public
     * @api
     */
    public function __construct($definitions = '', array $providers = [])
    {
        // load definitions from array or file
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
     * @inheritDoc
     */
    public function get($id)
    {
        if ($this->has($id)) {
            list($args, $scope, $sid) = $this->prepareGet($id, func_get_args());
            if (empty($args) && isset($this->pool[$sid])) {
                return $this->pool[$sid];
            } else {
                $service = $this->getService($id, $args);
                if (static::SCOPE_SINGLE !== $scope) {
                    $this->pool[$sid] = $service;
                }
                return $service;
            }
        } else {
            throw new NotFoundException(
                Message::get(Message::SERVICE_ID_NOT_FOUND, $id),
                Message::SERVICE_ID_NOT_FOUND
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function has($id)
    {
        return is_string($id) &&               // must be string
            (isset($this->services[$id])  ||    // in defintion
                $this->hasInProvider($id) ||    // OR in provider
                $this->autoWiringId($id)        // OR autowiring
            ) ? true : false;
    }

    /**
     * @inheritDoc
     */
    public function one(/*# string */ $id, array $arguments = [])
    {
        return $this->get($id, $arguments, self::SCOPE_SINGLE);
    }

    /**
     * @inheritDoc
     */
    public function run($callable, array $arguments = [])
    {
        return $this->executeCallable($callable, $arguments);
    }
}
