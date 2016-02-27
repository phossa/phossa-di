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

namespace Phossa\Di\Definition\Autowire;

/**
 * AutowiringTrait
 *
 * Impelementation of AutowiringInterface
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     AutowiringInterface
 * @version 1.0.6
 * @since   1.0.1 added
 */
trait AutowiringTrait
{
    /**
     * Autowiring ON or OFF. Default is on
     *
     * @var    bool
     * @access protected
     */
    protected $autowiring = true;

    /**
     * @inheritDoc
     */
    public function auto(/*# bool */ $switchOn)
    {
        $this->autowiring = (bool) $switchOn;
        return $this;
    }
}
