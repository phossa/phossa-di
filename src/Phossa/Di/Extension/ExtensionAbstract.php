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

use Phossa\Di\Message\Message;
use Phossa\Di\Exception\LogicException;

/**
 * ExtensionAbstract
 *
 * Base class for all extensions
 *
 * @abstract
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.4
 * @since   1.0.1 added
 */
abstract class ExtensionAbstract
{
    /**
     * @var    string
     */
    const EXTENSION_CLASS = __CLASS__;

    /**
     * Constructor, check EXTENION_CLASS set or not
     *
     * @throws LogicException if something goes wrong
     * @access public
     * @final
     * @internal
     */
    final public function __construct()
    {
        if (get_called_class() !== static::EXTENSION_CLASS) {
            throw new LogicException(
                Message::get(Message::EXTENION_INVALID_CLASS, get_class($this)),
                Message::EXTENION_INVALID_CLASS
            );
        }
    }

    /**
     * get classname
     *
     * @return string
     * @throws LogicException if class not set right
     * @access public
     * @internal
     */
    public function getName()/*# : string */
    {
        return static::EXTENSION_CLASS;
    }
}
