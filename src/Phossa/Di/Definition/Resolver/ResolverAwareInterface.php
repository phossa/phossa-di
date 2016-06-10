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

namespace Phossa\Di\Definition\Resolver;

use Phossa\Config\ParameterInterface;
use Phossa\Di\Exception\NotFoundException;

/**
 * ResolverAwareInterface
 *
 * Parameter resolver support
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.7
 * @since   1.0.7 added
 */
interface ResolverAwareInterface
{
    /**
     * Set parameter resolver if any
     *
     * ```php
     * // set a parameter resolver to resolve '%system.tmpdir%' etc.
     * $container->setResolver(
     *     (new Config())->setReferencePattern('%', '%')
     * );
     * ```
     *
     * @param  ParameterInterface $resolver
     * @return self
     * @access public
     * @api
     */
    public function setResolver(ParameterInterface $resolver);

    /**
     * Get the parameter resolver
     *
     * @return ParameterInterface
     * @throws NotFoundException if resolver not found
     * @access public
     * @api
     */
    public function getResolver()/*# : ParameterInterface */;

    /**
     * Has parameter resolver
     *
     * @return bool
     * @access public
     * @api
     */
    public function hasResolver()/*# : bool */;
}
