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

use Phossa\Di\Exception\LogicException;
use Phossa\Di\Exception\NotFoundException;
use Phossa\Di\Definition\Scope\ScopeInterface;
use Phossa\Di\Exception\InvalidArgumentException;
use Phossa\Di\Definition\Autowire\AutowiringInterface;

/**
 * DefinitionAwareInterface
 *
 * Definition support for the container
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.4
 * @since   1.0.1 added
 */
interface DefinitionAwareInterface extends ScopeInterface, AutowiringInterface
{
    /**
     * Set/replace parameter definition(s)
     *
     * ```php
     * // set a parameter
     * $container->set('cache.root', '/var/tmp');
     *
     * // pointing to another parameter
     * $container->set('cache.root', '%tmp.dir%');
     *
     * // set a parameter with an array
     * $container->set('cache', [
     *     'root' => '/var/tmp',
     *     'name' => 'session_cache',
     *     'lifetime' => 86400
     * ]);
     *
     * // set with array
     * $container->set([
     *     'cache' => [
     *         'root' => '/var/tmp',
     *         // ...
     *     ]
     * ]);
     * ```
     *
     * @param  string|array $name parameter name or parameter array
     * @param  string|array $value parameter value or values(array)
     * @return static
     * @throws InvalidArgumentException
     * @access public
     * @api
     */
    public function set($name, /*# string */ $value = '');

    /**
     * Add/overwrite service definition(s)
     *
     * ```php
     * // add a service with  classname
     * $container->add('cache', 'Phossa\\Cache\\CachePool');
     *
     * // add a service with a closue
     * $container->add('cache', function() {
     *     return new \Phossa\Cache\CachePool();
     * });
     *
     * // with constructor argument (MUST BE an array)
     * $container->add('cache', 'Phossa\\Cache\\CachePool', ['@driver@']);
     *
     * // alias, pointing to another service
     * $container->add('sessionCache', '@globalCache@');
     *
     * // set service to a callable
     * $container->add('logger', [$event, 'getLogger']);
     *
     * // or a pseudo callable
     * $container->add('logger', ['@event@', 'getLogger']);
     *
     * // add batch services
     * $container->add([
     *     'cache'  => 'Phossa\Cache\CachePool',
     *     'driver' => 'Phossa\Cache\Driver\FilesystemDriver',
     *     // ...
     * ]);
     * ```
     *
     * @param  string|array $id service id/classname or definition array
     * @param  string|callable $class (optional) classname or closure
     * @param  array $arguments (optional) constructor or callable arguments
     * @return static
     * @throws InvalidArgumentException
     * @access public
     * @api
     */
    public function add($id, $class = '', array $arguments = []);

    /**
     * Map an interface/classname to classname/service id/parameter
     *
     * ```php
     * // map a interface => a classname
     * $container->map(
     *     'Phossa\\Cache\\CachePoolInterface', // MUST NO leading backslash
     *     'Phossa\\Cache\\CachePool' // leading backslash is optional
     * );
     *
     * // map a interface => service reference
     * $container->map('Phossa\\Cache\\CachePoolInterface', '@cache@');
     *
     * // map a interface => a parameter => classname
     * $container->map('Phossa\\Cache\\CachePoolInterface', '%cache.class%');
     *
     * // batch mapping
     * $container->map([
     *     'Vendor1\InterfaceOne' => 'Vendor1\ClassOne',
     *     'Vendor2\InterfaceTwo' => 'MyOwnClassTwo',
     *     'Vendor2\ClassThree'   => 'MyOwnClassThree',
     *     // ...
     * ]);
     * ```
     *
     * @param  string|array $interface interface/classname or array
     * @param  string $classname classname/service id/parameter etc.
     * @return static
     * @throws InvalidArgumentException
     * @access public
     * @api
     */
    public function map($interface, /*# string */ $classname = '');

    /**
     * Load definitions from an array or a file
     *
     * @param  string|array $fileOrArray definition file or array
     * @return static
     * @throws NotFoundException if file not found
     * @throws LogicException if something goes wrong
     * @access public
     * @api
     */
    public function load($fileOrArray);

    /**
     * Add method call to the previous chained service `add()`
     *
     * ```php
     * $container->add('cache', 'Phossa\\Cache\\CachePool')
     *           ->addMethod('setLogger', [ '@logger@' ]);
     * ```
     *
     * @param  string $method method name
     * @param  array $arguments arguments for the method
     * @return static
     * @throws NotFoundException if no service id found
     * @access public
     * @api
     */
    public function addMethod(/*# string */ $method, array $arguments = []);

    /**
     * Set scope for the previous service
     *
     * ```php
     * // predefined scope
     * $container->add('cache', 'Phossa\\Cache\\CachePool')
     *           ->setScope(Container::SCOPE_SHARED);
     *
     * // shared in 'anotherScope' ONLY!
     * $container->add('logger', 'Phossa\\Logger\\Logger')
     *           ->setScope('anotherScope');
     * ```
     *
     * @param  string $scope scope value
     * @return static
     * @throws NotFoundException if no service id found
     * @access public
     * @api
     */
    public function setScope(/*# string */ $scope);
}
