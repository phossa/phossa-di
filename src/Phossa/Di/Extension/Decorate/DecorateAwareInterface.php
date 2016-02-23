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
 * @version 1.0.1
 * @since   1.0.1 added
 */
interface DecorateAwareInterface
{
    /**
     * Set decorating rule
     *
     * $tester: string (interface or classname) or callable
     * $decorator: ['method', [ args...]] or callable
     *
     * e.g.
     * <code>
     *    $this->setDecorate(
     *      'logger',
     *      '\\Phossa\\Logger\\LoggerAwareInterface', // interface
     *      [ 'setLogger', ['@logger@'] ] // method & arguments
     *    );
     *
     *    $container = $this;
     *    $this->setDecorate(
     *        'container',
     *        function($service) {
     *            return $service instanceof ContainerAwareInterface;
     *        },
     *        function($service) use ($container) {
     *            $service->setContainer($container);
     *        }
     *    );
     * </code>
     *
     * @param  string $name rule name
     * @param  string|callable $tester interface/classname or callable
     * @param  array|callable methods/arguments array or callable
     * @return void
     * @access public
     * @api
     */
    public function setDecorate(/*# string */ $name, $tester, $decorator);
}
