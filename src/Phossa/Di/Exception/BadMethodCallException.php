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

use Phossa\Shared\Exception\BadMethodCallException as Exception;

/**
 * BadMethodCallException for Phossa\Di
 *
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     ExceptionInterface
 * @see     \Phossa\Shared\Exception\BadMethodCallException
 * @version 1.0.4
 * @since   1.0.1 added
 */
class BadMethodCallException extends Exception implements ExceptionInterface
{
}
