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

namespace Phossa\Di\Extension;

use Phossa\Di\Exception\LogicException;

/**
 * ExtensionInterface
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.1
 * @since   1.0.1 added
 */
interface ExtensionInterface
{
    /**
     * Get extension name
     *
     * @return string
     * @throws LogicException if name not defined
     * @access public
     * @api
     */
    public function getName()/*# : string */;
}
