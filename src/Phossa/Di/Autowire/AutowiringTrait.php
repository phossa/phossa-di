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

namespace Phossa\Di\Autowire;

/**
 * AutowiringTrait
 *
 * Impelementation of AutowiringInterface
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     AutowiringInterface
 * @version 1.0.4
 * @since   1.0.1 added
 */
trait AutowiringTrait
{
    use \Phossa\Di\Definition\DefinitionAwareTrait;

    /**
     * Autowiring ON or OFF
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
        $this->autowiring = $switchOn;
        return $this;
    }

    /**
     * Auto wire the $id
     *
     * If not defined AND is a classname, add it to definition automatically
     *
     * @param  string $id
     * @return bool
     * @access protected
     */
    protected function autoWiringId(/*# string */ $id)/*# : bool */
    {
        if ($this->autowiring && class_exists($id)) {
            $this->add($id);
            return true;
        }
        return false;
    }
}
