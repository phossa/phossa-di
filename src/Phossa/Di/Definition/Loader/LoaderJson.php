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

use Phossa\Di\Message\Message;
use Phossa\Di\Exception\LogicException;

/**
 * LoaderJson
 *
 * Load definitions from files in JSON format
 *
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.6
 * @since   1.0.1 added
 */
class LoaderJson implements LoaderInterface
{
    /**
     * @inheritDoc
     *
     * services.s.json,
     * <code>
     * {
     *     'streamhandler': {
     *         'class' : [
     *               '\Phossa\Logger\Handler\StreamHandler',
     *               [ '%logger.file%', 'debug' ]
     *         ],
     *         'scope' : '__SINGLE__'
     *     },
     *     ...
     * }
     * </code>
     *
     * parameters.p.json,
     * <code>
     * {
     *     'logger': {
     *         'file':  '/var/local/app.log',
     *         'mail': {
     *             'to_address'   : 'webmaster@example.com',
     *             'from_address' : 'alerts@example.com',
     *             'subject'      : 'App Logs'
     *         }
     *     }
     * }
     * </code>
     */
    public static function loadFromFile(/*# string */ $file)/*# : array */
    {
        // readin json content
        $json = file_get_contents($file);
        if (false === $json) {
            throw new LogicException(
                Message::get(Message::DEFINITION_NOT_FOUND, $file),
                Message::DEFINITION_NOT_FOUND
            );
        }

        if (!is_array($definitions = json_decode($json, true))) {
            throw new LogicException(
                Message::get(Message::DEFINITION_FORMAT_ERR, $file),
                Message::DEFINITION_FORMAT_ERR
            );
        }
        return $definitions;
    }
}
