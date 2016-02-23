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

namespace Phossa\Di\Exception;

use Interop\Container\Exception\NotFoundException as NFException;

/**
 * Proxy to Interop NotFoundException interface
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Interop\Container\Exception\NotFoundException
 * @version 1.0.1
 * @since   1.0.1 added
 */
interface InteropNotFoundException extends NFException
{
}
