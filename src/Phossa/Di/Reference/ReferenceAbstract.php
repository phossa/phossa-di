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

namespace Phossa\Di\Reference;

/**
 * ReferenceAbstract
 *
 * @abstract
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.4
 * @since   1.0.1 added
 */
abstract class ReferenceAbstract
{
    /**
     * the name
     *
     * @var    string
     * @access protected
     */
    protected $name;

    /**
     * Constructor
     *
     * @param  string $name
     * @access public
     * @internal
     */
    public function __construct(/*# string */ $name)
    {
        $this->name = $name;
    }

    /**
     * Get the name
     *
     * @return string
     * @access public
     * @internal
     */
    public function getName()/*# : string */
    {
        return $this->name;
    }
}
