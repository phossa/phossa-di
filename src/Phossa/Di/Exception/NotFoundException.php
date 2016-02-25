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

use Phossa\Di\Interop\InteropNotFoundException;
use Phossa\Shared\Exception\NotFoundException as Exception;

/**
 * NotFoundException for Phossa\Di
 *
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     ExceptionInterface
 * @see     InteropNotFoundException
 * @see     \Phossa\Shared\Exception\NotFoundException
 * @version 1.0.4
 * @since   1.0.0 added
 */
class NotFoundException extends Exception implements
    ExceptionInterface,
    InteropNotFoundException
{
}
