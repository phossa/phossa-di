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

namespace Phossa\Di\Extension\Loader;

use Phossa\Di\Exception\LogicException;
use Phossa\Di\Exception\NotFoundException;

/**
 * LoadableInterface
 *
 * Load definition from files for the container using LoaderExtension
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.1
 * @since   1.0.1 added
 */
interface LoadableInterface
{
    /**
     * Load definitions from a file
     *
     * @param  string|array $fileOrArray definition file or array
     * @return void
     * @throws NotFoundException if file not found
     * @throws LogicException if something goes wrong
     * @access public
     * @api
     */
    public function load($fileOrArray);
}
