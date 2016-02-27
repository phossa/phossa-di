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

namespace Phossa\Di\Extension\Taggable;

use Phossa\Di\Extension\ExtensionAbstract;

/**
 * TaggableExtension
 *
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     ExtensionAbstract
 * @version 1.0.6
 * @since   1.0.1 added
 */
class TaggableExtension extends ExtensionAbstract
{
    /**
     * @inheritDoc
     */
    const EXTENSION_CLASS = __CLASS__;

    /**
     * tags registry
     *
     * @var    string[]
     * @access protected
     */
    protected $tags = [];

    /**
     * Set own tags
     *
     * @param  string[] $tags
     * @return void
     * @access public
     * @internal
     */
    public function setTags(array $tags)
    {
        $this->tags = array_unique($tags);
    }

    /**
     * Match with tags
     *
     * @param  string[] $tags
     * @return bool
     * @access public
     * @internal
     */
    public function matchTags(array $tags)/*# : bool */
    {
        return count(array_intersect($this->tags, $tags)) ? true : false;
    }
}
