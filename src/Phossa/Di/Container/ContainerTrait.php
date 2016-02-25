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

namespace Phossa\Di\Container;

use Phossa\Di\Message\Message;
use Phossa\Di\Exception\LogicException;

/**
 * CircularTrait
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.4
 * @since   1.0.1 added
 */
trait ContainerTrait
{
    use MainLogicTrait;

    /**
     * current service id cache for circular detection
     *
     * @var    string[]
     * @access protected
     */
    protected $circular = [];

    /**
     * counter
     *
     * @var    int
     * @access protected
     */
    protected $counter  = 0;

    /**
     * Process arguments for get()
     *
     * @param  string $id service id
     * @param  array $arguments of get()
     * @return array
     * @access protected
     */
    protected function prepareGet(/*# string */ $id, array $arguments)
    {
        $args  = isset($arguments[1]) ? (array)  $arguments[1] : [];
        $scope = isset($arguments[2]) ? (string) $arguments[2] :
            $this->getScope($id);

        // scope === '@ancestor_id@' ?
        if (isset($this->circular[$scope])) {
            $scope .= '#' . $this->circular[$scope];
        }

        return [ $args, $scope, $scope . '::' . $id ];
    }

    /**
     * Check circular, create service and decorate it
     *
     * @param  string $id service id
     * @param  array &$args constructor arguments
     * @return object
     * @access protected
     */
    protected function getService($id, &$args)
    {
        // check circular
        $this->checkCircular($id);

        // create the service
        $service = $this->createService($id, $args);

        // decorate the service
        $this->decorateService($service);

        // remove circular mark
        $this->removeCircular($id);

        return $service;
    }

    /**
     * Check circular for get()
     *
     * @param  string $id service id
     * @return void
     * @throws LogicException if circular found
     * @access protected
     */
    protected function checkCircular(/*# string */ $id)
    {
        // reference id "@$id@" of current service object
        $refId = $this->getReferenceId($id);

        // circular detection
        if (isset($this->circular[$refId])) {
            throw new LogicException(
                Message::get(Message::SERVICE_CIRCULAR, $id),
                Message::SERVICE_CIRCULAR
            );

        // not in loop
        } else {
            $this->circular[$refId] = ++$this->counter;
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

    /**
     * Return '@serviceId@'
     *
     * @param  string $id service id
     * @return string
     * @access protected
     */
    protected function getReferenceId(/*# string */ $id)/*# : string */
    {
        return '@' . $id . '@';
    }
}
