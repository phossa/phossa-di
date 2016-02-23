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

use Interop\Container\ContainerInterface as CTInterface;

/**
 * InteropContainerInterface
 *
 * Proxy to Interop or PSR ContainerInterface
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     ContainerInterface
 * @version 1.0.1
 * @since   1.0.1 added
 */
interface InteropContainerInterface extends CTInterface
{
}
