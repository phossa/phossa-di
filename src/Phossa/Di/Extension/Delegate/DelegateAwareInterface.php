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

use Phossa\Di\DelegatorInterface;

/**
 * DelegateAwareInterface
 *
 * Interop delegate support for the container using DelegateExtension
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.6
 * @since   1.0.1 added
 */
interface DelegateAwareInterface
{
    /**
     * Set the delegator
     *
     * ```php
     * use Phossa\Di\Delegator;
     *
     * // create the delegator
     * $delegator = new Delegator();
     *
     * // other container register with the delegator
     * $delegator->addContainer($otherContainer);
     *
     * // register self with delegator
     * $container->setDelegate($delegator);
     *
     * // will be resolved in the order of $otherContainer, $container
     * // ...
     * ```
     *
     * @param  DelegatorInterface $delegator
     * @return self
     * @access public
     * @api
     */
    public function setDelegate(DelegatorInterface $delegator);
}
