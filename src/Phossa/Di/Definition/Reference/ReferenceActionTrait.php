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

/**
 * ReferenceActionTrait
 *
 * All de-reference related method.
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.6
 * @since   1.0.4 added
 */
trait ReferenceActionTrait
{
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

                // dereference if it is a reference
                } elseif (($ref = $this->isReference($data))) {
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
     * Get this paramter's value either a string or an associate array
     *
     * ```php
     * $this->set('cache.dir', '/var/tmp');
     *
     * // will return an array ['dir' => '/var/tmp'];
     * $result = $this->getParameter('cache');
     *
     * // will return a string, 'var/tmp'
     * $result = $this->getParameter('cache.dir');
     * ```
     *
     * @param  string $name parameter name
     * @return string|array
     * @throws NotFoundException if not found
     * @access protected
     */
    protected function getParameter(/*# string */ $name)
    {
        // break into parts by '.'
        $parts = explode('.', $name);
        $found = $this->parameters;
        while (null !== ($part = array_shift($parts))) {
            if (!isset($found[$part])) {
                throw new NotFoundException(
                    Message::get(Message::PARAMETER_NOT_FOUND, $name),
                    Message::PARAMETER_NOT_FOUND
                );
            }
            $found = $found[$part];
        }
        return $found;
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
        // reference string pattern
        $pat = '/^(%|@)([^\s]+)\1$/';
        $mat = []; // placeholders

        // is a reference object
        if (is_object($data) && $data instanceof ReferenceAbstract) {
            return $data;

        // is string and matches reference pattern
        } elseif (is_string($data) && preg_match($pat, $data, $mat)) {
            return $mat[1] === '%' ?
                new ParameterReference($mat[2]) :
                new ServiceReference($mat[2]);

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
    abstract protected function delegatedGet($name);
}
