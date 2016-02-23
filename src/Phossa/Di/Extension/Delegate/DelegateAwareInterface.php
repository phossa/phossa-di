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
 * @version 1.0.1
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
     * @param  DelegatorInterface $delegator
     * @param  bool keepAutowiring keep autowiring of current container
     * @return void
     * @access public
     * @api
     */
    public function setDelegate(
        DelegatorInterface $delegator,
        /*# bool */ $keepAutowiring = false
    );
}
