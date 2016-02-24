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

namespace Phossa\Di\Definition;

use Phossa\Di\Message\Message;
use Phossa\Di\Exception\LogicException;
use Phossa\Di\Exception\NotFoundException;

/**
 * ResolvableTrait
 *
 * Impelementation of ResolvableInterface
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     ResolvableInterface
 * @version 1.0.1
 * @since   1.0.1 added
 */
trait ResolvableTrait
{
    use DefinitionAwareTrait,
        \Phossa\Di\Extension\ExtensibleTrait,
        \Phossa\Di\Extension\Delegate\DelegateAwareTrait;

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
    public function auto(/*# bool */ $status)/*# : ResolvableInterface */
    {
        $this->autowiring = $status;
        return $this;
    }

    /**
     * Autowiring a id if it equals to a known classname
     *
     * @param  string $id
     * @return bool
     * @access protected
     */
    protected function autoWiringId(/*# string */ $id)/*# : bool */
    {
        // if autowiring is TRUE and $is is a class, register/add $id
        if ($this->autowiring && class_exists($id)) {
            $this->add($id);
            return true;
        }
        return false;
    }

    /**
     * Resolve a fake callable to a real one
     *
     * fake callable like `[ '@cache@', 'setLogger' ]` or
     * `[ new ServiceReference('cache'), 'setLogger' ]`
     *
     * @param  array|callable $callable
     * @return callable
     * @throws LogicException
     * @throws NotFoundException
     * @access protected
     */
    protected function resolveCallable($callable)
    {
        // resolve pseudo callable
        if (is_array($callable)) {
            // resolve method if it is a parameter
            if (false !== ($ref = $this->isParameterReference($callable[1]))) {
                $callable[1] = $ref->getName();
            }

            // resolve object if it is a reference string
            if (is_string($callable[0]) &&
                ($res = $this->isServiceReference($callable[0]))) {
                $callable[0] = $this->delegatedGet($res->getName());
            }

            // resolve object if it is a reference object
            if (is_object($callable[0]) &&
                $callable[0] instanceof ReferenceAbstract
            ) {
                $callable[0] = $this->delegatedGet($callable[0]->getName());
            }
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
        // reflect method
        if (is_object($callable)) {
            $reflector = new \ReflectionClass($callable);
            $method = $reflector->getMethod('__invoke');

        // [ object/class, method ]
        } elseif (is_array($callable)) {
            $reflector = new \ReflectionClass($callable[0]);
            $method = $reflector->getMethod($callable[1]);

        // function
        } else {
            $method = new \ReflectionFunction($callable);
        }

        return $this->resolveArguments($method, $arguments);
    }

    /**
     * Execute callable with arguments
     *
     * @param  mixed $callable callable or pseudo callable
     * @param  array $arguments
     * @return void
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
     * @param  array &$arguments
     * @return void
     * @throws LogicException if something goes wrong
     * @access protected
     */
    protected function dereferenceArguments(array &$arguments)
    {
        try {
            foreach ($arguments as $idx => $arg) {
                if (is_array($arg)) {
                    $this->dereferenceArguments($arguments[$idx]);
                } elseif (false !== ($ref = $this->isServiceReference($arg))) {
                    $arguments[$idx] = $this->delegatedGet($ref->getName());
                } elseif (false !== ($ref = $this->isParameterReference($arg))) {
                    $val = $this->getParameter($ref->getName());
                    /*
                    // circular is possible here
                    if (is_object($val) && $val instanceof \Closure) {
                        $val = $this->executeCallable($val);
                    }
                     */
                    $arguments[$idx] = $val;
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
                        if (false != ($ref = $this->isServiceReference($name)) ||
                            false != ($ref = $this->isParameterReference($name))
                        ) {
                            $name = $ref->getName();
                        }
                    }

                    // not a id, neither a classname
                    if (!$this->delegatedHas($name)) {
                        throw new NotFoundException(
                            Message::get(Message::CLASS_NOT_FOUND, $name),
                            Message::CLASS_NOT_FOUND
                        );
                    }

                    // put in place
                    array_splice(
                        $arguments, $i, 0, [ $this->delegatedGet($name) ]
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

            // closure with possible default arguments
            } elseif (is_callable($class[0])) {
                $instance = $this->executeCallable($class[0], $args);

            // instantiation with arguments
            } else {
                $class = $class[0];
                $reflector   = new \ReflectionClass($class);
                $constructor = $reflector->getConstructor();

                // not constructor defined
                if (is_null($constructor)) {
                    $instance = new $class();

                    // __invoke() defined
                    if (count($args) && method_exists($class, '__invoke')) {
                        $this->executeCallable([$instance, '__invoke'], $args);
                    }

                // singleton
                } elseif (!$constructor->isPublic() &&
                    method_exists($class, 'getInstance')) {
                    $instance = $class::getInstance();

                    // __invoke() defined
                    if (count($args) && method_exists($class, '__invoke')) {
                        $this->executeCallable([$instance, '__invoke'], $args);
                    }

                // normal class with constructor
                } else {
                    $args = $this->resolveArguments($constructor, $args);
                    $instance = $reflector->newInstanceArgs($args);
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
                    isset($method[1]) ? $method[1] : []
                );
            }
        } catch (\Exception $e) {
            throw new LogicException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Is argument a service reference ? '@serviceId@'
     *
     * @param  mixed $argument the argument to check
     * @return ServiceReference|false
     * @access protected
     */
    protected function isServiceReference($argument)
    {
        $pat = '/^@([a-zA-Z_\x7f-\xff][\w\x7f-\xff]*)@$/';
        $mat = [];
        if (is_object($argument) && $argument instanceof ServiceReference) {
            return $argument;
        } elseif (is_string($argument) && preg_match($pat, $argument, $mat)) {
            return new ServiceReference($mat[1]);
        } else {
            return false;
        }
    }

    /**
     * Generate '@serviceId@'
     *
     * @param  string $id service id
     * @return string
     * @access protected
     */
    protected function getReferenceId(/*# string */ $id)/*# : string */
    {
        return '@' . $id . '@';
    }

    /**
     * Is argument a parameter reference ? '%parameter.name%'
     *
     * @param  mixed $argument the argument to check
     * @return ParameterReference|false
     * @access protected
     */
    protected function isParameterReference($argument)
    {
        $pat = '/^%([a-zA-Z_\x7f-\xff][.\w\x7f-\xff]*)%$/';
        $mat = [];
        if (is_object($argument) && $argument instanceof ParameterReference) {
            return $argument;
        } elseif (is_string($argument) && preg_match($pat, $argument, $mat)) {
            return new ParameterReference($mat[1]);
        } else {
            return false;
        }
    }
}
