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

namespace Phossa\Di\Extension\Provider;

use Phossa\Di\ContainerAwareInterface;
use Phossa\Di\Exception\LogicException;
use Phossa\Di\InteropContainerInterface;

/**
 * ProviderInterface
 *
 * Container definition management through provider, tags supported
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     InteropContainerInterface
 * @see     ContainerAwareInterface
 * @version 1.0.1
 * @since   1.0.1 added
 */
interface ProviderInterface extends
    ContainerAwareInterface,
    InteropContainerInterface
{
    /**
     * Merge definitions managed by this provider into the container
     *
     * @return void
     * @throws LogicException if merging goes wrong
     * @access public
     * @api
     */
    public function merge();

    /**
     * Does this provider provide something ?
     *
     * @return bool
     * @access public
     * @api
     */
    public function isProviding()/*# : bool */;
}
