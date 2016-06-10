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

namespace Phossa\Di\Definition\Reference;

use Phossa\Di\Message\Message;
use Phossa\Di\Exception\LogicException;
use Phossa\Di\Exception\NotFoundException;
use Phossa\Di\Definition\Resolver\ResolverAwareTrait;

/**
 * ReferenceActionTrait
 *
 * All de-reference related method.
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.7
 * @since   1.0.4 added
 * @since   1.0.7 added support for resolver
 */
trait ReferenceActionTrait
{
    use ResolverAwareTrait;

    /**
     * Replace all the references in the array with values
     *
     * @param  array &$arrayData
     * @return void
     * @throws LogicException if something goes wrong
     * @access protected
     */
    protected function dereferenceArray(array &$arrayData)
    {
        try {
            foreach ($arrayData as $idx => $data) {
                // go deeper if is array
                if (is_array($data)) {
                    $this->dereferenceArray($arrayData[$idx]);

                // dereference if it is a reference #changed
                } elseif (false !== ($ref = $this->isReference($data))) {
                    $arrayData[$idx] = $this->getReferenceValue($ref);
                }
            }
        } catch (\Exception $e) {
            throw new LogicException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get the reference value
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
        if ($level > 2) {
            throw new NotFoundException(
                Message::get(Message::PARAMETER_LOOP_FOUND, $name),
                Message::PARAMETER_LOOP_FOUND
            );
        }

        // get service reference value
        if ($reference instanceof ServiceReference) {
            return $this->delegatedGet($name);

        // get parameter value, if value is another reference, go deeper
        } else {
            $val = $this->getParameter($name);
            if (is_string($val) && ($ref = $this->isReference($val))) {
                return $this->getReferenceValue($ref, ++$level);
            }
            return $val;
        }
    }

    /**
     * Is a reference string or reference object. convert to object
     *
     * @param  mixed $data data to check
     * @return ReferenceAbstract|false
     * @access protected
     */
    protected function isReference($data)
    {
        // is a reference object
        if (is_object($data) && $data instanceof ReferenceAbstract) {
            return $data;

        // is string and matches reference pattern
        } elseif (is_string($data)) {
            $pat = '/^(@|%)([^\s]+)\1$/';
            $mat = []; // placeholders

            if (preg_match($pat, $data, $mat)) {
                return $mat[1] === '@' ?
                    new ServiceReference($mat[2]) :
                    new ParameterReference($mat[2]);
            }

            // parameter resolver support
            if ($this->hasResolver() &&
                $this->getResolver()->hasReference($data)) {

                list($s, $e) = $this->paramter_pattern;
                $pat = '/^' . preg_quote($s) . '([^\s]+)' . preg_quote($e). '$/';
                if (preg_match($pat, $data, $mat)) {
                    return new ParameterReference($mat[1]);
                }
            }

            return false;

        // not a match
        } else {
            return false;
        }
    }

    /**
     * Return '@serviceId@'
     *
     * @param  string $id service id
     * @return string
     * @access protected
     */
    protected function getServiceReferenceId(/*# string */ $id)/*# : string */
    {
        return '@' . $id . '@';
    }

    /*
     * Get value method used in this trait
     */
    abstract protected function delegatedGet(/*# string */ $name);
    abstract protected function getParameter(/*# string */ $name);
}
