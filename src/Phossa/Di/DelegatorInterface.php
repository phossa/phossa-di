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

use Phossa\Di\Interop\InteropContainerInterface;

/**
 * DelegatorInterface
 *
 * Manage Interop containers, providing `get()` and `has()`
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     InteropContainerInterface
 * @see     https://github.com/container-interop/fig-standards/blob/master/proposed/container.md#13-additional-feature-delegate-lookup
 * @version 1.0.6
 * @since   1.0.1 added
 */
interface DelegatorInterface extends InteropContainerInterface
{
    /**
     * Append container to the pool end
     *
     * @param  InteropContainerInterface $container
     * @return static
     * @access public
     * @api
     */
    public function addContainer(InteropContainerInterface $container);

    /**
     * Get all containers in an array
     *
     * @return InteropContainerInterface[]
     * @access public
     * @api
     */
    public function getContainers()/*# : array */;
}
