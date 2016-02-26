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
use Phossa\Di\Exception\InvalidArgumentException;

/**
 * DefinitionAwareTrait
 *
 * Implementation of DefinitionAwareInterface
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     DefinitionAwareInterface
 * @version 1.0.4
 * @since   1.0.1 added
 */
trait DefinitionAwareTrait
{
    use Scope\ScopeTrait,
        Loader\LoadableTrait,
        Autowire\AutowiringTrait;

    /**
     * parameter definitions
     *
     * @var    array
     * @access protected
     */
    protected $parameters   = [];

    /**
     * service definitions
     *
     * @var    array
     * @access protected
     */
    protected $services     = [];

    /**
     * interface to class OR interface/class to id mapping!
     *
     * @var    array
     * @access protected
     */
    protected $mappings     = [];

    /**
     * service id after add() called
     *
     * @var    string
     * @access protected
     */
    protected $last_added   = '';

    /**
     * @inheritDoc
     */
    public function set($name, /*# string */ $value = '')
    {
        if (is_array($name)) {
            $this->fixParameters($name);
            $this->parameters = array_replace_recursive(
                $this->parameters,
                $name
            );
        } elseif (is_string($name)) {
            $this->set([$name => $value]);
        } else {
            throw new InvalidArgumentException(
                Message::get(Message::PARAMETER_ID_INVALID, gettype($name)),
                Message::PARAMETER_ID_INVALID
            );
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function add($id, $class = '', array $arguments = [])
    {
        $this->last_added = '';
        if (is_array($id)) {
            $this->fixServices($id);
            $this->services = array_replace($this->services, $id);
        } elseif (is_string($id)) {
            $this->services[$id] = ['class' => [ $class ?: $id, $arguments ]];
            $this->last_added = $id;
        } else {
            throw new InvalidArgumentException(
                Message::get(Message::SERVICE_ID_INVALID, gettype($id)),
                Message::SERVICE_ID_INVALID
            );
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function map($interface, /*# string */ $classname = '')
    {
        if (is_array($interface)) {
            $this->mappings = array_replace($this->mappings, $interface);
        } elseif (is_string($interface)) {
            $this->mappings[$interface] = $classname;
        } else {
            throw new InvalidArgumentException(
                Message::get(Message::MAP_ID_INVALID, gettype($interface)),
                Message::MAP_ID_INVALID
            );
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function load($fileOrArray)
    {
        $loaded = false;

        // load from file
        if (is_string($fileOrArray)) {
            return $this->load($this->loadFile($fileOrArray));

        // load from array
        } elseif (is_array($fileOrArray)) {
            $toload = [
                'services'      => 'add',
                'parameters'    => 'set',
                'mappings'      => 'map'
            ];
            foreach($toload as $key => $action) {
                if (isset($fileOrArray[$key])) {
                    $this->$action($fileOrArray[$key]);
                    $loaded = true;
                }
            }
        }

        // not loaded
        if (!$loaded) {
            throw new LogicException(
                Message::get(Message::DEFINITION_FORMAT_ERR),
                Message::DEFINITION_FORMAT_ERR
            );
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addMethod(/*# string */ $method, array $arguments = [])
    {
        if (empty($this->last_added)) {
            throw new NotFoundException(
                Message::get(Message::SERVICE_ID_NOT_FOUND, __METHOD__),
                Message::SERVICE_ID_NOT_FOUND
            );
        } else {
            $service = &$this->services[$this->last_added];
            if (!isset($service['methods'])) {
                $service['methods'] = [];
            }
            $service['methods'][] = [ $method, $arguments ];
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setScope(/*# string */ $scope)
    {
        if (empty($this->last_added)) {
            throw new NotFoundException(
                Message::get(Message::SERVICE_ID_NOT_FOUND, __METHOD__),
                Message::SERVICE_ID_NOT_FOUND
            );
        } else {
            $this->services[$this->last_added]['scope'] = $scope;
        }
        return $this;
    }

    /**
     * Get one paramter, dereference upto 10 levels
     *
     * @param  string $name parameter name
     * @param  int $level current dereference level
     * @return string|array
     * @throws NotFoundException if not found
     * @access protected
     */
    protected function getParameter(/*# string */ $name, $level = 0)
    {
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

        // dereference loop
        if (is_string($found) &&
            '%s' === substr($found, 0, 1) &&
            '%s' === substr($found, -1)) {
            if ($level > 9) {
                throw new NotFoundException(
                    Message::get(Message::PARAMETER_LOOP_FOUND, $name),
                    Message::PARAMETER_LOOP_FOUND
                );
            }
            return $this->getParameter(substr($found, 1, -1), ++$level);
        }

        return $found;
    }

    /**
     * Get the scope for service $id
     *
     * If not set, get the default scope
     *
     * @param  string $id service id
     * @return string
     * @access protected
     */
    protected function getScope(/*# string */ $id)/*# : string */
    {
        if (isset($this->services[$id]['scope'])) {
            return (string) $this->services[$id]['scope'];
        } else {
            return $this->default_scope;
        }
    }

    /**
     * fix paramters
     *
     * @param  array &$data parameter data
     * @return void
     * @access protected
     */
    protected function fixParameters(array &$data)
    {
        foreach ($data as $name => $value) {
            if (is_array($value)) {
                $this->fixParameters($value);
            }
            $parts  = explode('.', $name);
            $result = &$data;
            while (null !== ($part = array_shift($parts))) {
                if (count($parts)) {
                    if (!isset($result[$part])) {
                        $result[$part] = [];
                    }
                    $result = &$result[$part];
                } else {
                    $result[$part] = $value;
                }
            }
        }
    }

    /**
     * Normalize service definitions
     *
     * @param  array &$definitions
     * @return void
     * @access protected
     */
    protected function fixServices(array &$definitions)
    {
        foreach ($definitions as $id => $def) {
            if (is_array($def)) {
                if (!isset($def['class'])) {
                    $def = ['class' => $def];
                } elseif (!is_array($def['class'])) {
                    $def['class'] = [ $def['class'] ];
                }
            } else {
                $def = ['class' => [ $def ]];
            }
            $definitions[$id] = $def;
        }
    }

    /**
     * With auto wiring is on, add service $id if it is a classname
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
