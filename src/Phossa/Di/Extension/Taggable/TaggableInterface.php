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

namespace Phossa\Di\Extension\Taggable;

use Phossa\Di\Exception\InvalidArgumentException;

/**
 * TaggableInterface
 *
 * Tag support for the container using TaggableExtension
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.4
 * @since   1.0.1 added
 */
interface TaggableInterface
{
    /**
     * Set container tags
     *
     * @param  string|string[] $tags
     * @return static
     * @throws InvalidArgumentException
     * @access public
     * @api
     */
    public function setTag($tags);

    /**
     * Check container tags
     *
     * @param  string|string[] $tags
     * @return bool
     * @access public
     * @api
     */
    public function hasTag($tags)/*# : bool */;
}
