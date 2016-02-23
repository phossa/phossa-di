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

use Phossa\Di\Message\Message;
use Phossa\Di\Exception\LogicException;

/**
 * LoaderPhp
 *
 * Load service/parameter definitions from files in PHP format
 *
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.1
 * @since   1.0.1 added
 */
class LoaderPhp implements LoaderInterface
{
    /**
     * {@inheritDoc}
     *
     * services.s.php
     * <code>
     * <?php
     * use Phossa\Logger;
     * use Psr\Log\LogLevel;
     *
     * return [
     *     'streamhandler' => [
     *         'class' => [
     *              Logger\Handler\StreamHandler::class,
     *              [ '%logger.file%', LogLevel::WARNING ],
     *         ],
     *         'methods' => [
     *              [ 'setFormatter', ['@ansiFormatter@'] ]
     *         ]
     *     ],
     *
     *     'logger' => [
     *         'class' => [
     *             Logger\Logger::class,
     *             [ '%logger.channel%' ]
     *         ],
     *         'methods' => [
     *             [ 'addHandler', [ '@streamhandler@' ] ],
     *             [ 'addDecorator', [ '@interpolate@' ] ],
     *             [ 'addDecorator', [ '@profiler@' ] ]
     *         ],
     *     ],
     *
     *     'ansiFormatter' => [
     *         'class' => function() {
     *              return new Logger\Formatter\AnsiFormatter();
     *         },
     *         'scope' => 'instance'
     *     ],
     *
     *     // use closure directly
     *     'interpolate' => function() {
     *         return new Logger\Decorator\InterpolateDecorator();
     *     },
     *
     *     // use array directly
     *     'profiler' => [
     *         Logger\Decorator\ProfileHandler::class
     *     ],
     *     ...
     * ];
     * </code>
     *
     * parameters.p.php,
     * <code>
     * <?php
     * return [
     *     'logger' => [
     *         'channel' =>  'myLogger',
     *         'file'    =>  __DIR__.'/../app.log',
     *         'mail'    => [
     *             'to_address'   => 'webmaster@example.com',
     *             'from_address' => 'alerts@example.com',
     *             'subject' => 'App Logs'
     *         ]
     *     ]
     * ];
     * </code>
     */
    public static function loadFromFile(/*# string */ $file)/*# : array */
    {
        if (!is_array($definitions = @include $file)) {
            throw new LogicException(
                Message::get(Message::DEFINITION_NOT_FOUND, $file),
                Message::DEFINITION_NOT_FOUND
            );
        }
        return $definitions;
    }
}
