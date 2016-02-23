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

use Phossa\Di\Extension\Loader\LoaderExtension;
use Phossa\Di\Extension\Taggable\TaggableExtension;
use Phossa\Di\Extension\Provider\ProviderExtension;
use Phossa\Di\Extension\Delegate\DelegateExtension;
use Phossa\Di\Extension\Decorate\DecorateExtension;

/**
 * List of avaiable extensions
 *
 * Map name/id to extension class, used for autmatically create extensions
 * 
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.1
 * @since   1.0.1 added
 */
class ExtensionMap
{
    /**
     * mapping
     *
     * @var    array
     * @access public
     * @static
     */
    public static $mapping = [
        LoaderExtension::EXTENSION_NAME   => LoaderExtension::EXTENSION_CLASS,
        TaggableExtension::EXTENSION_NAME => TaggableExtension::EXTENSION_CLASS,
        ProviderExtension::EXTENSION_NAME => ProviderExtension::EXTENSION_CLASS,
        DelegateExtension::EXTENSION_NAME => DelegateExtension::EXTENSION_CLASS,
        DecorateExtension::EXTENSION_NAME => DecorateExtension::EXTENSION_CLASS,
    ];
}
