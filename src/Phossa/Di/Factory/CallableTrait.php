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

/**
 * CallableTrait
 *
 * Execute callable related methods
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.4
 * @since   1.0.4 added
 */
trait CallableTrait
{
    use DereferenceTrait;

    /**
     * Execute a (pseudo) callable with arguments
     *
     * @param  callable|array $callable callable or pseudo callable
     * @param  array $arguments
     * @return mixed
     * @throws LogicException if something goes wrong
     * @access protected
     */
    protected function executeCallable($callable, array $arguments)
    {
        // resolve fake callable
        $call = $this->resolveCallable($callable);

        // execute the callable
        return call_user_func_array(
            $call,
            $this->resolveCallableArguments($call, $arguments)
        );
    }

    /**
     * Resolve a fake callable to a real one
     *
     * fake callable like `[ '@cache@', 'setLogger' ]` or
     * `[ new ServiceReference('cache'), 'setLogger' ]`
     *
     * @param  callable|array $callable
     * @return callable
     * @throws LogicException
     * @throws NotFoundException
     * @access protected
     */
    protected function resolveCallable($callable)
    {
        if (is_array($callable)) {
            $this->dereferenceArray($callable);
        }

        // normal callable
        if (is_callable($callable)) {
            return $callable;
        } else {
            throw new LogicException(
                Message::get(Message::CALLABLE_INVALID, $callable),
                Message::CALLABLE_INVALID
            );
        }
    }

    /**
     * Resolve arguments for a callable
     *
     * @param  callable $callable
     * @param  array $arguments
     * @return array the resolved arguments
     * @throws LogicException
     * @throws NotFoundException
     * @access protected
     */
    protected function resolveCallableArguments(
        callable $callable,
        array $arguments
    )/*# : array */ {
        // get \ReflectionMethod
        if (is_object($callable)) {
            $reflector = new \ReflectionClass($callable);
            $method = $reflector->getMethod('__invoke');

        } elseif (is_array($callable)) {
            $reflector = new \ReflectionClass($callable[0]);
            $method = $reflector->getMethod($callable[1]);

        } else {
            $method = new \ReflectionFunction($callable);
        }

        // dereference the provided arguments
        $this->dereferenceArray($arguments);

        // compare and match method parameters with arguments
        return $this->matchArguments($method->getParameters(), $arguments);
    }

    /**
     * Match arguments base on reflection
     *
     * @param  \ReflectionParameter[] $params
     * @param  array $arguments
     * @return array
     * @throws LogicException
     * @throws NotFoundException
     * @access protected
     */
    protected function matchArguments($params, array $arguments)/*# : array */
    {
        $result = [];
        $fail   = false;
        foreach ($params as $i => $param) {
            $optional = $param->isOptional();
            $argument = isset($arguments[0]) ? $arguments[0] : null;
            if ($optional && is_null($argument)) {
                break;
            }

            // class/interface
            if (($class = $param->getClass())) {
                $classname = $class->getName();
                if (!$this->matchType($classname, $argument)) {
                    $result[$i] = $this->getObject($classname);
                } else {
                    $result[$i] = array_shift($arguments);
                }

            // other types
            } else {
                if (!$optional && is_null($argument)) {
                    $fail = $i;
                    break;
                } else {
                    $result[$i] = array_shift($arguments);
                }
            }
        }

        if (false !== $fail) {
            throw new LogicException(
                Message::get(Message::PARAMETER_NOT_FOUND, $fail),
                Message::PARAMETER_NOT_FOUND
            );
        }

        return $result;
    }

    /**
     * Detailed is_a()
     *
     * @param  string $type
     * @param  mixed $argument
     * @return bool
     * @access protected
     */
    protected function matchType(/*# string */ $type, $argument)/*# : bool */
    {
        if (is_object($argument) && is_a($argument, $type)) {
            return true;
        }
        return false;
    }

    /**
     * Get an object baseon provided classname
     *
     * @param  string $classname class name
     * @return object
     * @throws LogicException
     * @access protected
     * @api
     */
    protected function getObject(/*# string */ $classname)
    {
        // check mappings
        if (isset($this->mappings[$classname])) {
            $classname = $this->mappings[$classname];
            if (($ref = $this->isReference($classname))) {
                $classname = $this->getReferenceValue($ref);
                if (is_object($classname)) {
                    return $classname;
                }
            }
        }
        return $this->delegatedAction($classname, 'get');
    }
}
