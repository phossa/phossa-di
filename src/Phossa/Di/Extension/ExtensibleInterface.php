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

/**
 * ExtensibleInterface
 *
 * Extension support for the container, including all predefined extension
 * related api methods. Extension loaded automatically once user call one
 * of the api methods in the extensions.
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     Loader\LoadableInterface
 * @see     Taggable\TaggableInterface
 * @see     Provider\ProviderAwareInterface
 * @see     Delegate\DelegateAwareInterface
 * @see     Decorate\DecorateAwareInterface
 * @version 1.0.4
 * @since   1.0.1 added
 */
interface ExtensibleInterface extends Loader\LoadableInterface, Taggable\TaggableInterface, Provider\ProviderAwareInterface, Delegate\DelegateAwareInterface, Decorate\DecorateAwareInterface
{
    /**
     * Add extension to the container
     *
     * @param  ExtensionAbstract $extension
     * @return static
     * @access public
     * @api
     */
    public function addExtension(ExtensionAbstract $extension);
}
