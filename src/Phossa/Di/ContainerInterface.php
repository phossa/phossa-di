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

use Phossa\Di\Exception\LogicException;

/**
 * ContainerInterface
 *
 * Extended phossa-di container interface, Provides one() and run() methods
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     Extension\ExtensibleInterface
 * @see     Interop\InteropContainerInterface
 * @see     Definition\DefinitionAwareInterface
 * @version 1.0.4
 * @since   1.0.1 added
 */
interface ContainerInterface extends Interop\InteropContainerInterface, Extension\ExtensibleInterface, Definition\DefinitionAwareInterface
{
    /**
     * Get a new service even if it was defined as shared
     *
     * Arguments can have references as follows
     *
     * ```php
     * // reference string
     * $service = $container->one('cache', ['@driver@']);
     *
     * // reference object
     * $service = $container->one('cache', [new ServiceReference('driver')]);
     *
     * // parameters are allowed
     * $service = $container->one('cache', ['@driver@', '%cache.name%']);
     * ```
     *
     * @param  string $id service id
     * @param  array $arguments (optional) arguments
     * @return object
     * @throws LogicException
     * @access public
     * @api
     */
    public function one(/*# string */ $id, array $arguments = []);

    /**
     * Execute a callable, expands its arguments
     *
     * $callable can be a pseudo callable as follows,
     *
     * ```php
     * // pseudo callable using service reference string
     * $container->run(['@cache@', 'setLogger'], ['@logger@']);
     *
     * // pseduo callable using service reference object
     * $container->run([new ServiceReference('cache'), 'setLogger'], [$logger]);
     *
     * // method can be a parameter
     * $container->run([$cache, '%log.setter%'], [$logger]);
     * ```
     *
     * @param  callable|array $callable
     * @param  array $arguments (optional) arguments
     * @return mixed
     * @throws LogicException if DI goes wrong
     * @throws \Exception if execution goes wrong
     * @access public
     * @api
     */
    public function run($callable, array $arguments = []);
}
