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

namespace Phossa\Di\Autowire;

/**
 * AutowiringInterface
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.4
 * @since   1.0.1 added
 */
interface AutowiringInterface
{
    /**
     * Set autowiring ON or OFF
     *
     * @param  bool $switchOn
     * @return static
     * @access public
     * @api
     */
    public function auto(/*# bool */ $switchOn);
}
