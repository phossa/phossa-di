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

namespace Phossa\Di\Definition;

use Phossa\Di\Exception\NotFoundException;
use Phossa\Di\Exception\InvalidArgumentException;

/**
 * DefinitionAwareInterface
 *
 * Definition support for the container
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.1
 * @since   1.0.1 added
 */
interface DefinitionAwareInterface
{
    /**
     * object shared
     */
    const SCOPE_SHARED = '__SHARED__';

    /**
     * object has to be created each time
     */
    const SCOPE_SINGLE = '__SINGLE__';

    /**
     * Set parameter definition
     *
     * @param  string|array $name parameter name or parameter array
     * @param  string $value parameter value
     * @return DefinitionAwareInterface this
     * @throws InvalidArgumentException
     * @access public
     * @api
     */
    public function set(
        $name,
        /*# string */ $value = ''
    )/*# : DefinitionAwareInterface */;

    /**
     * Add service definition
     *
     * Add or overwrite service definition
     *
     * @param  string|array $id service id/classname or definition array
     * @param  string|callable $class (optional) classname or closure
     * @param  array $arguments (optional) arguments
     * @return DefinitionAwareInterface this
     * @throws InvalidArgumentException
     * @access public
     * @api
     */
    public function add(
        $id,
        $class = '',
        array $arguments = []
    )/*# : DefinitionAwareInterface */;

    /**
     * Interface to classname map
     *
     * Map interface to classname, interface or classname to service id
     *
     * @param  string|array $interface interface/classname or array
     * @param  string $classname classname or service id
     * @return DefinitionAwareInterface this
     * @throws InvalidArgumentException
     * @access public
     * @api
     */
    public function map(
        $interface,
        /*# string */ $classname = ''
    )/*# : DefinitionAwareInterface */;

    /**
     * Add method call to the previous service
     *
     * @param  string $method method name
     * @param  array $arguments arguments for the method
     * @return DefinitionAwareInterface this
     * @throws NotFoundException if no service id found
     * @access public
     * @api
     */
    public function addMethod(
        /*# string */ $method,
        array $arguments = []
    )/*# : DefinitionAwareInterface */;

    /**
     * Set scope for the previous service
     *
     * @param  string $scope scope value
     * @return DefinitionAwareInterface this
     * @throws NotFoundException if no service id found
     * @access public
     * @api
     */
    public function setScope(
        /*# string */ $scope
    )/*# : DefinitionAwareInterface */;
}
