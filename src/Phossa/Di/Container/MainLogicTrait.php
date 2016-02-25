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

namespace Phossa\Di\Container;

use Phossa\Di\Message\Message;
use Phossa\Di\Exception\LogicException;
use Phossa\Di\Reference\ServiceReference;
use Phossa\Di\Exception\NotFoundException;
use Phossa\Di\Reference\ReferenceAbstract;
use Phossa\Di\Reference\ParameterReference;
use Phossa\Di\Extension\Delegate\DelegateExtension;

/**
 * MainLogicTrait
 *
 * Main logic for create objects
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.1
 * @since   1.0.1 added
 */
trait MainLogicTrait
{
    use \Phossa\Di\Extension\ExtensibleTrait;

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
        // resolve pseudo callable
        if (is_array($callable)) {
            $this->dereferenceArguments($callable);
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
     * @param  array $arguments default arguments
     * @return array the resolved arguments
     * @throws LogicException
     * @throws NotFoundException
     * @access protected
     */
    protected function resolveCallableArguments(
        callable $callable,
        array $arguments
    )/*# : array */ {
        if (is_object($callable)) {
            $reflector = new \ReflectionClass($callable);
            $method = $reflector->getMethod('__invoke');

        } elseif (is_array($callable)) {
            $reflector = new \ReflectionClass($callable[0]);
            $method = $reflector->getMethod($callable[1]);

        } else {
            $method = new \ReflectionFunction($callable);
        }

        return $this->resolveArguments($method, $arguments);
    }

    /**
     * Execute callable with arguments
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
     * Replace all the references in the arguments
     *
     * @param  array|string &$args
     * @return void
     * @throws LogicException if something goes wrong
     * @access protected
     */
    protected function dereferenceArguments(&$args)
    {
        try {
            // string
            if (is_string($args)) {
                $todo = [ &$args ];
                $this->dereferenceArguments($todo);
            } else {
                foreach ($args as $idx => $arg) {
                    if (is_array($arg)) {
                        $this->dereferenceArguments($args[$idx]);
                    } elseif (($ref = $this->isReference($arg))) {
                        $args[$idx] = $ref instanceof ParameterReference ?
                            $this->getParameter($ref->getName()) :
                            $this->delegatedAction($ref->getName(), 'get');
                    }
                }
            }
        } catch (\Exception $e) {
            throw new LogicException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Resolve arguments base on reflection
     *
     * @param  \ReflectionFunctionAbstract $method method to check
     * @param  array $arguments
     * @return array
     * @throws LogicException
     * @throws NotFoundException
     * @access protected
     */
    protected function resolveArguments(
        \ReflectionFunctionAbstract $method,
        array $arguments
    )/*# : array */ {
        // dereference $arguments
        $this->dereferenceArguments($arguments);

        // method parameter definition
        $params = $method->getParameters();

        // no arguments required
        if (0 === count($params)) {
            return $arguments;
        }

        // compare param one by one
        foreach ($params as $i => $param) {
            /* @var $param \ReflectionParameter */
            if ($param->isOptional() && !isset($arguments[$i])) {
                break;
            }

            // class or instance type-hinted ?
            $class = $param->getClass();

            if (is_null($class)) {
                if (!isset($arguments[$i])) {
                    throw new LogicException(
                        Message::get(
                            Message::ARGUMENT_MISSING,
                            $param->getName()
                        ),
                        Message::ARGUMENT_MISSING
                    );
                }
            } else {
                // class/interface name
                $name = $class->getName();

                // argument matches
                if (isset($arguments[$i]) &&
                    is_object($arguments[$i]) &&
                    $arguments[$i] instanceof $name
                ) {
                    continue;

                // autowiring this argument
                } else {
                    // miss-match ?
                    if (count($params) == count($arguments)) {
                        throw new LogicException(
                            Message::get(
                                Message::PARAMETER_TYPE_WRONG,
                                $name,
                                get_class($arguments[$i])
                            ),
                            Message::PARAMETER_NOT_FOUND
                        );
                    }

                    // try mappings
                    if (isset($this->mappings[$name])) {
                        $name = $this->mappings[$name];
                        if (($ref = $this->isReference($name))) {
                            $name = $ref instanceof ServiceReference ?
                                $ref->getName() :
                                $this->getParameter($ref->getName());
                        }
                    }

                    // put in place
                    array_splice(
                        $arguments, $i, 0,
                        [ $this->delegatedAction($name, 'get') ]
                    );
                }
            }
        }

        return $arguments;
    }

    /**
     * Create service
     *
     * @param  string $id service id
     * @param  array $arguments
     * @return object
     * @throws LogicException if something goes wrong
     * @access protected
     */
    protected function createService(/*# string */ $id, array $arguments)
    {
        // get definition
        $class = $this->services[$id]['class'];

        // fix arguments
        $args = empty($arguments) ?
                (is_array($class) && isset($class[1]) ? $class[1] : []) :
                $arguments;

        try {
            // closure
            if (is_object($class) && $class instanceof \Closure) {
                $instance = $this->executeCallable($class, $args);

            // (pseudo) callable with possible default arguments
            } elseif (is_callable($class[0]) || is_array($class[0])) {
                $instance = $this->executeCallable($class[0], $args);

            // instantiation with arguments
            } else {
                $class = $class[0];

                // reference
                if (($ref = $this->isReference($class))) {
                    if ($ref instanceof ParameterReference) {
                        $class = $this->getParameter($ref->getName());
                    } else {
                        // alias
                        return $this->delegatedAction($ref->getName(), 'get');
                    }
                }

                $invoke = false;
                $reflector = new \ReflectionClass($class);
                $constructor = $reflector->getConstructor();

                // not constructor defined
                if (is_null($constructor)) {
                    $instance = new $class();
                    $invoke =  true;
                // singleton
                } elseif (!$constructor->isPublic() &&
                    method_exists($class, 'getInstance')) {
                    $instance = $class::getInstance();
                    $invoke =  true;

                // normal class with constructor
                } else {
                    $args = $this->resolveArguments($constructor, $args);
                    $instance = $reflector->newInstanceArgs($args);
                }

                // __invoke() defined
                if ($invoke && count($args) &&
                    method_exists($class, '__invoke')) {
                    $this->executeCallable([$instance, '__invoke'], $args);
                }
            }
        } catch (\Exception $e) {
            throw new LogicException($e->getMessage(), $e->getCode(), $e);
        }

        // error
        if (!isset($instance)) {
            throw new LogicException(
                Message::get(Message::DEFINITION_FORMAT_ERR, $id),
                Message::DEFINITION_FORMAT_ERR
            );
        }

        // method call ?
        if (isset($this->services[$id]['methods'])) {
            $this->initService($id, $instance, $this->services[$id]['methods']);
        }

        return $instance;
    }

    /**
     * Initialize instance by runing its methods
     *
     * @param  string $id service id
     * @param  object $instance service object
     * @param  array $methods method array
     * @return void
     * @throws LogicException
     * @access protected
     */
    protected function initService(
        /*# string */ $id,
        $instance,
        array $methods
    ) {
        try {
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
                    [ $instance, $method[0] ],
                    isset($method[1]) ? (array) $method[1] : []
                );
            }
        } catch (\Exception $e) {
            throw new LogicException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Is a reference
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
