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

namespace Phossa\Di;

use Phossa\Di\Message\Message;
use Phossa\Shared\Pattern\StaticAbstract;
use Phossa\Di\Exception\BadMethodCallException;

/**
 * StaticContainer
 *
 * Static wrapper for container
 * 
 * @static
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     StaticAbstract
 * @version 1.0.1
 * @since   1.0.1 added
 */
class StaticContainer extends StaticAbstract
{
    /**
     * @var    InteropContainerInterface
     * @access protected
     * @static
     */
    protected static $container;

    /**
     * Provides a static interface for all methods
     *
     * @param  string $name method name
     * @param  array $arguments arguments
     * @return mixed
     * @access public
     * @static
     */
    public static function __callStatic($name, array $arguments)
    {
        $container = static::getContainer();
        if (method_exists($container, $name)) {
            return call_user_func_array([$container, $name], $arguments);
        }

        throw new BadMethodCallException(
            Message::get(
                Message::METHOD_NOT_FOUND,
                get_called_class(),
                $name
            ),
            Message::METHOD_NOT_FOUND
        );
    }

    /**
     * Set container if you want
     *
     * @param  InteropContainerInterface $container
     * @return void
     * @access public
     * @static
     * @api
     */
    public static function setContainer(InteropContainerInterface $container)
    {
        self::$container = $container;
    }

    /**
     * Get container, create one if not set yet
     *
     * @return InteropContainerInterface $container
     * @access public
     * @static
     * @api
     */
    public static function getContainer()
    {
        if (is_null(self::$container)) {
            self::$container = new Container();
        }
        return self::$container;
    }
}
