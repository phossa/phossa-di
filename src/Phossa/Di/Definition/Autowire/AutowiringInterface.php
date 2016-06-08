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

namespace Phossa\Di\Definition\Autowire;

/**
 * AutowiringInterface
 *
 * Auto wiring is the ability of container instantiating objects and resolving
 * its dependencies automatically. System will look into $this->mappings for
 * interface to classname mappings.
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.6
 * @since   1.0.1 added
 */
interface AutowiringInterface
{
    /**
     * Set autowiring ON or OFF
     *
     * @param  bool $switchOn
     * @return self
     * @access public
     * @api
     */
    public function auto(/*# bool */ $switchOn);
}
