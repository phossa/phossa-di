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

use Phossa\Di\Exception\LogicException;

/**
 * ProviderAwareInterface
 *
 * Provider support for the container using ProviderExtension
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.6
 * @since   1.0.1 added
 */
interface ProviderAwareInterface
{
    /**
     * Add providers to container
     *
     * @param  string|ProviderAbstract $providerOrClass
     * @return self
     * @throws LogicException if not a valide provider
     * @access public
     * @api
     */
    public function addProvider($providerOrClass);
}
