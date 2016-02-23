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

use Phossa\Shared\Exception\ExceptionInterface as PhossaException;

/**
 * Exception marker for Phossa\Di
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     InteropContainerException
 * @see     \Phossa\Shared\Exception\ExceptionInterface
 * @version 1.0.1
 * @since   1.0.1 added
 */
interface ExceptionInterface extends InteropContainerException, PhossaException
{
}
