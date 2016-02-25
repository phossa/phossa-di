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

namespace Phossa\Di\Container;

use Phossa\Di\ContainerInterface;
use Phossa\Di\Exception\NotFoundException;

/**
 * ContainerAwareInterface
 *
 * Inject container into implementing class
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.4
 * @since   1.0.1 added
 */
interface ContainerAwareInterface
{
    /**
     * Set the container
     *
     * @param  ContainerInterface $container
     * @return static
     * @access public
     * @api
     */
    public function setContainer(ContainerInterface $container);

    /**
     * Get the container
     *
     * @return ContainerInterface
     * @throws NotFoundException
     * @access public
     * @api
     */
    public function getContainer()/*# : ContainerInterface */;

    /**
     * Has the container ?
     *
     * @return bool
     * @access public
     * @api
     */
    public function hasContainer()/*# : bool */;
}
