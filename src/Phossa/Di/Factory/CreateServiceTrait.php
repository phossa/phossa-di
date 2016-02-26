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
 * CreateServiceTrait
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.4
 * @since   1.0.4 added
 */
trait CreateServiceTrait
{
    use CallableTrait;

    /**
     * Create service object
     *
     * @param  string $id service id
     * @param  array $arguments
     * @return object
     * @throws LogicException if something goes wrong
     * @access protected
     */
    protected function createService(/*# string */ $id, array $arguments)
    {
        // classname & args
        $def   = &$this->services[$id]['class'];
        $class = $def[0];
        $args  = empty($arguments) ?
            (isset($def[1]) ? $def[1] : []) :
            $arguments;

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
     * Instantiate service object
     *
     * @param  string $class
     * @param  array $args
     * @return object
     * @throws LogicException if something goes wrong
     * @access protected
     */
    protected function constructObject(/*# string */ $class, array $args)
    {
        $invoke = false;
        $reflector = new \ReflectionClass($class);
        $constructor = $reflector->getConstructor();

        // dereference arguments
        $this->dereferenceArray($args);

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
            $args = $this->matchArguments(
                $constructor->getParameters(),
                $args
            );
            return $reflector->newInstanceArgs($args);
        }

        // __invoke() defined
        if ($invoke && count($args) &&
            method_exists($class, '__invoke')) {
            $this->executeCallable([$instance, '__invoke'], $args);
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
}
