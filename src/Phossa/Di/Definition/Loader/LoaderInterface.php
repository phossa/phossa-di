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

namespace Phossa\Di\Definition\Loader;

use Phossa\Di\Exception\LogicException;

/**
 * LoaderInterface
 *
 * Load definitions from file
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.4
 * @since   1.0.1 added
 */
interface LoaderInterface
{
    /**
     * Load definitions from a file
     *
     * @param  string $file file name
     * @return array
     * @throws LogicException if something goes wrong
     * @access public
     * @static
     * @api
     */
    public static function loadFromFile(/*# string */ $file)/*# : array */;
}
