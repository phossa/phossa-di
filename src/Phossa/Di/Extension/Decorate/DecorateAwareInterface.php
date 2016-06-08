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

namespace Phossa\Di\Extension\Decorate;

/**
 * DecorateAwareInterface
 *
 * Decorate/modifying the service object using predefined callable(decorator)
 * base on the result of a tester callable
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.6
 * @since   1.0.1 added
 */
interface DecorateAwareInterface
{
    /**
     * Set decorating rule
     *
     *
     * ```php
     * // any object implementing 'LoggerAwareInterface' should be decorated
     * $container->addDecorate(
     *     'setlogger',  // rule name
     *     'Psr\\Log\\LoggerAwareInterface', // NO leading backslash
     *     ['setLogger', ['@logger@']] // run this method
     * );
     * ```
     *
     * Or
     *
     * ```php
     * $container->addDecorate('setlogger',
     *     function($object) {
     *         return $object instanceof \Psr\Log\LoggerAwareInterface;
     *     },
     *     function($object) use($container) {
     *         $object->setLogger($container->get('logger'));
     *     }
     * );
     * ```
     *
     * @param  string $ruleName decorate rule name
     * @param  string|callable $interfaceOrClosure interface/classname or callable
     * @param  array|callable $decorateCallable
     * @return self
     * @access public
     * @api
     */
    public function addDecorate(
        /*# string */ $ruleName,
        $interfaceOrClosure,
        $decorateCallable
    );
}
