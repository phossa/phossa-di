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
 * @see     ExtensionInterface
 * @version 1.0.1
 * @since   1.0.1 added
 */
abstract class ExtensionAbstract implements ExtensionInterface
{
    /**
     * Extension name, has to be redefined in child classes
     *
     * @const
     */
    const EXTENSION_NAME    = 'extension';

    /**
     * Extension class, has to be redefined in child classes
     *
     * @const
     */
    const EXTENSION_CLASS   = __CLASS__;

    /**
     * {@inheritDoc}
     */
    public function getName()/*# : string */
    {
        // has to be redefined in child class
        if ('extension' === static::EXTENSION_NAME) {
            throw new LogicException(
                Message::get(Message::EXTENION_INVALID_NAME, get_class($this)),
                Message::EXTENION_INVALID_NAME
            );
        }

        // has to be redefined in child class also
        if (get_called_class() !== static::EXTENSION_CLASS) {
            throw new LogicException(
                Message::get(Message::EXTENION_INVALID_CLASS, get_class($this)),
                Message::EXTENION_INVALID_CLASS
            );
        }

        return static::EXTENSION_NAME;
    }
}
