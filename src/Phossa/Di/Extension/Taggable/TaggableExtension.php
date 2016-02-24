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
 * @version 1.0.1
 * @since   1.0.1 added
 */
class TaggableExtension extends ExtensionAbstract
{
    /**
     * Extension class, has to be redefined in child classes
     *
     * @const
     */
    const EXTENSION_CLASS   = __CLASS__;

    /**
     * tags registry
     *
     * @var    string[]
     * @access protected
     */
    protected $tags = [];

    /**
     * Set tags
     *
     * @param  string[] $tags
     * @return void
     * @access public
     */
    public function setTags(array $tags)
    {
        $this->tags = array_unique(array_merge($this->tags, $tags));
    }

    /**
     * Match with tags
     *
     * TRUE:
     *   - if $tags is empty
     *   - $this->tags and $tags has matching tag(s)
     *
     * @param  string[] $tags
     * @return bool
     * @access public
     */
    public function matchTags(array $tags)/*# : bool */
    {
        if (empty($tags)) {
            return true;
        } else {
            return count(array_intersect($this->tags, $tags)) ? true : false;
        }
    }
}
