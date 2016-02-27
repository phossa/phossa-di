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

/**
 * ServiceFactoryTrait
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.6
 * @since   1.0.4 added
 */
trait ServiceFactoryTrait
{
    use \Phossa\Di\Extension\ExtensibleTrait,
        \Phossa\Di\Definition\DefinitionAwareTrait;

    /**
     * Execute a (pseudo) callable with arguments
     *
     * @param  callable|array $callable callable or pseudo callable
     * @param  array $arguments
     * @return mixed
     * @throws LogicException if something goes wrong
     * @access protected
     */
    protected function executeCallable($callable, array $arguments = [])
    {
        // resolve a pseudo callable to real callable
        $call = $this->resolvePseudoCallable($callable);

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
     * @param  callable|array $callableOrArray
     * @return callable
     * @throws LogicException
     * @throws NotFoundException
     * @access protected
     */
    protected function resolvePseudoCallable($callableOrArray)
    {
        if (is_array($callableOrArray)) {
            $this->dereferenceArray($callableOrArray);
        }

        // normal callable
        if (is_callable($callableOrArray)) {
            return $callableOrArray;
        } else {
            throw new LogicException(
                Message::get(Message::CALLABLE_INVALID, $callableOrArray),
                Message::CALLABLE_INVALID
            );
        }
    }

    /**
     * Resolve arguments for a callable
     *
     * @param  callable $callable
     * @param  array $providedArguments the provided arguments
     * @return array the resolved arguments
     * @throws LogicException
     * @throws NotFoundException
     * @access protected
     */
    protected function resolveCallableArguments(
        callable $callable,
        array $providedArguments
    )/*# : array */ {
        // object with __invoke defined or \Closure
        if (is_object($callable)) {
            $reflector = new \ReflectionClass($callable);
            $method = $reflector->getMethod('__invoke');

        // array-like callable
        } elseif (is_array($callable)) {
            $reflector = new \ReflectionClass($callable[0]);
            $method = $reflector->getMethod($callable[1]);

        // simple function
        } else {
            $method = new \ReflectionFunction($callable);
        }

        // dereference the provided arguments
        $this->dereferenceArray($providedArguments);

        // match provided arguments with method parameter definitions
        return $this->matchMethodArguments(
            $method->getParameters(),
            $providedArguments
        );
    }

    /**
     * Match provided arguments with a method/function's reflection parameters
     *
     * @param  \ReflectionParameter[] $ReflectionParameters
     * @param  array $providedArguments
     * @return array the resolved arguments
     * @throws LogicException
     * @throws NotFoundException
     * @access protected
     */
    protected function matchMethodArguments(
        array $ReflectionParameters,
        array $providedArguments
    )/*# : array */ {
        $resolvedArguments = [];
        $fail   = false;

        // go thru each parameter defined
        foreach ($ReflectionParameters as $i => $param) {
            $optional = $param->isOptional();
            $argument = isset($providedArguments[0]) ?
                $providedArguments[0] : null;

            // break out if optional & not in provided arguments
            if ($optional && is_null($argument)) {
                break;
            }

            // parameter is a class or interface
            if (($class = $param->getClass())) {
                $classname = $class->getName();

                // $argument is a instance of $classname
                if ($this->matchObjectWithClass($classname, $argument)) {
                    $resolvedArguments[$i] = array_shift($providedArguments);

                // get an object by its class/interface
                } else {
                    $resolvedArguments[$i] =
                        $this->getObjectByClassname($classname);
                }

            // other types
            } else {
                if (!$optional && is_null($argument) ||
                    !$this->matchArgumentType($param, $argument)
                ) {
                    $fail = $param->getName();
                    break;
                } else {
                    $resolvedArguments[$i] = array_shift($providedArguments);
                }
            }
        }

        if (false !== $fail) {
            throw new LogicException(
                Message::get(Message::PARAMETER_NOT_FOUND, $fail),
                Message::PARAMETER_NOT_FOUND
            );
        }

        return $resolvedArguments;
    }

    /**
     * Simple match of callable & array if defined in $ReflectionParameter
     *
     * @param  \ReflectionParameter $ReflectionParameter
     * @param  mixed $argumentToMatch
     * @return bool
     * @access protected
     */
    protected function matchArgumentType(
        \ReflectionParameter $ReflectionParameter,
        $argumentToMatch
    )/*# : bool */ {
        if ($ReflectionParameter->isCallable()) {
            return is_callable($argumentToMatch) ? true : false;
        } elseif ($ReflectionParameter->isArray()) {
            return is_array($argumentToMatch) ? true : false;
        } else {
            return true;
        }
    }

    /**
     * Detailed is_a()
     *
     * @param  string $classOrInterface
     * @param  mixed $objectToMatch
     * @return bool
     * @access protected
     */
    protected function matchObjectWithClass(
        /*# string */ $classOrInterface,
        $objectToMatch
    )/*# : bool */ {
        if (is_object($objectToMatch) &&
            is_a($objectToMatch, $classOrInterface))
        {
            return true;
        }
        return false;
    }

    /**
     * Get an object base on provided classname
     *
     * @param  string $classname class name
     * @return object
     * @throws LogicException
     * @access protected
     * @api
     */
    protected function getObjectByClassname(/*# string */ $classname)
    {
        // mapping exists
        if (isset($this->mappings[$classname])) {
            $classname = $this->mappings[$classname];

            // is it a reference ?
            if (($ref = $this->isReference($classname))) {
                $classname = $this->getReferenceValue($ref);

                // got a service object
                if (is_object($classname)) {
                    return $classname;
                }
            }
        }

        // try get service by $classname from container
        return $this->delegatedGet($classname);
    }

    /**
     * Create service object from service definition
     *
     * @param  string $id service id
     * @param  array $arguments
     * @return object
     * @throws LogicException if something goes wrong
     * @access protected
     */
    protected function createServiceObject(/*# string */ $id, array $arguments)
    {
        // class definition
        $def   = &$this->services[$id]['class'];
        $class = $def[0];

        // prepare constructor arguments
        $args  = empty($arguments) ?
            (isset($def[1]) ? $def[1] : []) : $arguments;

        try {
            // closure with arguments
            if (is_object($class) && $class instanceof \Closure) {
                return $this->executeCallable($class, $args);

            // (pseudo) callable with arguments
            } elseif (is_array($class[0])) {
                return $this->executeCallable($class[0], $args);

            // instantiation with arguments
            } else {
                // reference value
                if (($ref = $this->isReference($class))) {
                    $class = $this->getReferenceValue($ref);
                    if (is_object($class)) {
                        return $class;
                    }
                }

                return $this->constructObject($class, $args);
            }
        } catch (\Exception $e) {
            throw new LogicException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Instantiate service object from classname
     *
     * @param  string $class
     * @param  array $constructorArguments
     * @return object
     * @throws LogicException if something goes wrong
     * @access protected
     */
    protected function constructObject(
        /*# string */ $class,
        array $constructorArguments
    ) {
        $invoke = false;
        $reflector = new \ReflectionClass($class);
        $constructor = $reflector->getConstructor();

        // dereference arguments
        $this->dereferenceArray($constructorArguments);

        // not constructor defined
        if (is_null($constructor)) {
            $instance = new $class();
            $invoke = true;

        // singleton
        } elseif (!$constructor->isPublic() &&
            method_exists($class, 'getInstance')) {
            $instance = $class::getInstance();
            $invoke = true;

        // normal class with constructor
        } else {
            $constructorArguments = $this->matchMethodArguments(
                $constructor->getParameters(),
                $constructorArguments
            );
            $instance = $reflector->newInstanceArgs($constructorArguments);
        }

        // __invoke() defined
        if ($invoke && count($constructorArguments) &&
            method_exists($class, '__invoke')) {
            $this->executeCallable(
                [$instance, '__invoke'],
                $constructorArguments
            );
        }

        return $instance;
    }

    /**
     * Initialize service object by runing its defined methods
     *
     * @param  string $id service id
     * @param  object $service service object
     * @return void
     * @throws LogicException
     * @access protected
     */
    protected function runDefinedMethods(/*# string */ $id, $service)
    {
        try {
            if (isset($this->services[$id]['methods'])) {
                $methods = $this->services[$id]['methods'];
                
                foreach ($methods as $method) {
                    if (!is_array($method) ||
                        !isset($method[0]) ||
                        !is_string($method[0])
                    ) {
                        throw new LogicException(
                            Message::get(
                                Message::SERVICE_METHOD_ERROR,
                                $id,
                                isset($method[0]) ? $method[0] : ''
                            ),
                            Message::SERVICE_METHOD_ERROR
                        );
                    }

                    // execute with arguments
                    $this->executeCallable(
                        [ $service, $method[0] ],
                        isset($method[1]) ? (array) $method[1] : []
                    );
                }
            }
        } catch (\Exception $e) {
            throw new LogicException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
