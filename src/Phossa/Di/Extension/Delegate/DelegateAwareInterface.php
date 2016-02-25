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

namespace Phossa\Di\Extension\Delegate;

/**
 * DelegateAwareInterface
 *
 * Interop delegate support for the container using DelegateExtension
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.4
 * @since   1.0.1 added
 */
interface DelegateAwareInterface
{
    /**
     * Set the delegator
     *
     * Usually autowiring (auto registering known class into service) WILL
     * break delegator model. User MAY set the **LAST CONTAINER** in the
     * delegator autowiring.
     *
     * ```php
     * use Phossa\Di\Extension\Delegate\Delegator;
     *
     * // create the delegator
     * $delegator = new Delegator();
     *
     * // other container register with the delegator
     * $delegator->addContainer($otherContainer);
     *
     * // register self with delegator and keep autowiring ON
     * $container->setDelegate($delegator, true);
     * 
     * // will be resolved in the order of $otherContainer, $container
     * // ...
     * ```
     *
     * @param  DelegatorInterface $delegator
     * @param  bool keepAutowiring keep auto wiring of current container
     * @return static
     * @access public
     * @api
     */
    public function setDelegate(
        DelegatorInterface $delegator,
        /*# bool */ $keepAutowiring = false
    );
}
