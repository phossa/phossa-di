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

namespace Phossa\Di\Message;

use Phossa\Shared\Message\MessageAbstract;

/**
 * Message class for Phossa\Di
 *
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.1
 * @since   1.0.0 added
 */
class Message extends MessageAbstract
{
    /**#@+
     * @var   int
     */

    /**
     * Container not found for "%s"
     */
    const CONTAINER_NOT_FOUND   = 1602190630;

    /**
     * Service id "%s" not found
     */
    const SERVICE_ID_NOT_FOUND  = 1602190631;

    /**
     * provides[] not defined in "%s"
     */
    const EXT_PROVIDER_ERROR    = 1602190632;

    /**
     * Extension class not defined for "%s"
     */
    const EXTENION_INVALID_CLASS= 1602190633;

    /**
     * Definition file "%s" not found
     */
    const DEFINITION_NOT_FOUND  = 1602190634;

    /**
     * Definition file suffix "%s" unknown
     */
    const FILE_SUFFIX_UNKNOWN   = 1602190635;

    /**
     * Definition "%s" format error
     */
    const DEFINITION_FORMAT_ERR = 1602190636;

    /**
     * Parameter "%s" not found
     */
    const PARAMETER_NOT_FOUND   = 1602190637;

    /**
     * Callable "%s" not valid
     */
    const CALLABLE_INVALID      = 1602190638;

    /**
     * Argument missing for "%s"
     */
    const ARGUMENT_MISSING      = 1602190639;

    /**
     * Class or interface "%s" not found
     */
    const CLASS_NOT_FOUND       = 1602190640;

    /**
     * Service "%s" method "%s" error
     */
    const SERVICE_METHOD_ERROR  = 1602190641;

    /**
     * Try adding same provider "%s" again
     */
    const EXT_PROVIDER_DUPPED   = 1602190642;

    /**
     * Expect parameter type "%s" got "%s"
     */
    const PARAMETER_TYPE_WRONG  = 1602190643;

    /**
     * Circular loop found for service "%s"
     */
    const SERVICE_CIRCULAR      = 1602190644;

    /**
     * Method call "%s" not found
     */
    const METHOD_NOT_FOUND      = 1602190645;

    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected static $messages = [
        self::CONTAINER_NOT_FOUND   => 'Container not found for "%s"',
        self::SERVICE_ID_NOT_FOUND  => 'Service id "%s" not found',
        self::EXT_PROVIDER_ERROR    => 'provides[] not defined in "%s"',
        self::EXTENION_INVALID_CLASS=> 'Extension class not defined for "%s"',
        self::DEFINITION_NOT_FOUND  => 'Definition file "%s" not found',
        self::FILE_SUFFIX_UNKNOWN   => 'Definition file suffix "%s" unknown',
        self::DEFINITION_FORMAT_ERR => 'Definition "%s" format error',
        self::PARAMETER_NOT_FOUND   => 'Parameter "%s" not found',
        self::CALLABLE_INVALID      => 'Callable "%s" not valid',
        self::ARGUMENT_MISSING      => 'Argument missing for "%s"',
        self::CLASS_NOT_FOUND       => 'Class or interface "%s" not found',
        self::SERVICE_METHOD_ERROR  => 'Service "%s" method "%s" error',
        self::EXT_PROVIDER_DUPPED   => 'Try adding same provider "%s" again',
        self::PARAMETER_TYPE_WRONG  => 'Expect parameter type "%s" got "%s"',
        self::SERVICE_CIRCULAR      => 'Circular loop found for service "%s"',
        self::METHOD_NOT_FOUND      => 'Method call "%s" not found',
    ];
}
