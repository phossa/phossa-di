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

namespace Phossa\Di\Factory;

use Phossa\Di\Message\Message;
use Phossa\Di\Exception\LogicException;
use Phossa\Di\Exception\NotFoundException;
use Phossa\Di\Extension\Delegate\DelegateExtension;
use Phossa\Di\Definition\Reference\ServiceReference;
use Phossa\Di\Definition\Reference\ReferenceAbstract;
use Phossa\Di\Definition\Reference\ParameterReference;

/**
 * DereferenceTrait
 *
 * All dereference related method.
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.4
 * @since   1.0.4 added
 */
trait DereferenceTrait
{
    use \Phossa\Di\Extension\ExtensibleTrait,
        \Phossa\Di\Definition\DefinitionAwareTrait;

    /**
     * Replace all the reference string in the array with values
     *
     * @param  array &$args
     * @return void
     * @throws LogicException if something goes wrong
     * @access protected
     */
    protected function dereferenceArray(array &$args)
    {
        try {
            foreach ($args as $idx => $arg) {
                if (is_array($arg)) {
                    $this->dereferenceArray($args[$idx]);
                } elseif (($ref = $this->isReference($arg))) {
                    $args[$idx] = $this->getReferenceValue($ref);
                }
            }
        } catch (\Exception $e) {
            throw new LogicException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get reference value
     *
     * @param  ReferenceAbstract $reference
     * @param  int $level current recursive level
     * @return mixed
     * @access protected
     */
    protected function getReferenceValue(
        ReferenceAbstract $reference,
        /*# int */ $level = 0
    ) {
        $name = $reference->getName();

        // loop found
        if ($level > 5) {
            throw new NotFoundException(
                Message::get(Message::PARAMETER_LOOP_FOUND, $name),
                Message::PARAMETER_LOOP_FOUND
            );
        }

        if ($reference instanceof ServiceReference) {
            return $this->delegatedAction($name, 'get');
        } else {
            $val = $this->getParameter($name);
            if (is_string($val) && ($ref = $this->isReference($val))) {
                return $this->getReferenceValue($ref, ++$level);
            }
            return $val;
        }
    }

    /**
     * Is a reference string or object, convert to object
     *
     * @param  mixed $argument the argument to check
     * @return ReferenceAbstract|false
     * @access protected
     */
    protected function isReference($argument)
    {
        $pat = '/^(%|@)([^\s]+)\1$/';
        $mat = []; // placeholders
        if (is_object($argument) && $argument instanceof ReferenceAbstract) {
            return $argument;
        } elseif (is_string($argument) && preg_match($pat, $argument, $mat)) {
            return $mat[1] === '%' ?
                new ParameterReference($mat[2]) :
                new ServiceReference($mat[2]);
        } else {
            return false;
        }
    }

    /**
     * Try has()/get() from delegator if DelegateExtension loaded
     *
     * @param  string $id
     * @param  string $action 'get' or 'has'
     * @return bool|object
     * @throws NotFoundException
     * @access protected
     */
    protected function delegatedAction(
        /*# string */ $id, /*# string */ $action
    ) {
        $extName = DelegateExtension::EXTENSION_CLASS;
        if ($this->hasExtension($extName)) {
            /* @var $ext DelegateExtension */
            $ext = $this->getExtension($extName);
            return $ext->getDelegator()->$action($id);
        } else {
            return $this->$action($id);
        }
    }
}
