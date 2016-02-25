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

use Phossa\Di\Interop\InteropContainerInterface;

/**
 * DelegatorInterface
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     InteropContainerInterface
 * @version 1.0.4
 * @since   1.0.1 added
 */
interface DelegatorInterface extends InteropContainerInterface
{
    /**
     * Add to container pool end
     *
     * @param  InteropContainerInterface $container
     * @return void
     * @access public
     * @api
     */
    public function addContainer(InteropContainerInterface $container);

    /**
     * Get delegated containers
     *
     * @return InteropContainerInterface[]
     * @access public
     * @api
     */
    public function getContainers()/*# : array */;
}
