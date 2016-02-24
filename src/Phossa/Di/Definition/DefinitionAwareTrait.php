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
 * DefinitionAwareTrait
 *
 * Implementation of DefinitionAwareInterface
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     DefinitionAwareInterface
 * @version 1.0.1
 * @since   1.0.1 added
 */
trait DefinitionAwareTrait
{
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
     * {@inheritDoc}
     */
    public function set(
        $name,
        /*# string */ $value = ''
    )/*# : DefinitionAwareInterface */ {
        if (is_array($name)) {
            $this->fixParameters($name);
            $this->parameters = array_replace_recursive(
                $this->parameters,
                $name
            );
        } else {
            if (false === strpos($name, '.') && !is_array($value)) {
                $this->parameters[$name] = $value;
            } else {
                $this->set([$name => $value]);
            }
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function add(
        $id,
        $class = '',
        array $arguments = []
    )/*# : DefinitionAwareInterface */ {
        if (is_array($id)) {
            $this->fixServices($id);
            $this->services = array_replace($this->services, $id);
            $this->last_added = '';
            return $this;
        } elseif ('' === $class) {
            $class = $id;
        }

        $this->services[$id] = empty($arguments) ?
            ['class' => [ $class ]] :
            ['class' => [ $class, $arguments ]];

        $this->last_added = $id;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function map(
        $interface,
        /*# string */ $classname = ''
    )/*# : DefinitionAwareInterface */ {
        if (is_array($interface)) {
            $this->mappings = array_replace($this->mappings, $interface);
        } else {
            $this->mappings[$interface] = $classname;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addMethod(
        /*# string */ $method,
        array $arguments = []
    )/*# : DefinitionAwareInterface */ {
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
     * {@inheritDoc}
     */
    public function setScope(
        /*# string */ $scope
    )/*# : DefinitionAwareInterface */ {
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
     * Get one paramter
     *
     * @param  string $name parameter name
     * @return string|callable|array
     * @throws NotFoundException if not found
     * @access protected
     */
    protected function getParameter(/*# string */ $name)
    {
        $result = false;
        if (false === strpos($name, '.')) {
            if (isset($this->parameters[$name])) {
                $result = $this->parameters[$name];
            }
        } else {
            $parts  = explode('.', $name);
            $result = $this->parameters;
            while (null !== ($part = array_shift($parts))) {
                if (!isset($result[$part])) {
                    $result = false;
                    break;
                }
                $result = $result[$part];
            }
        }

        if (false === $result) {
            throw new NotFoundException(
                Message::get(Message::PARAMETER_NOT_FOUND, $name),
                Message::PARAMETER_NOT_FOUND
            );
        }
        return $result;
    }

    /**
     * Add scope in front of $id
     *
     * @param  string $id service id
     * @return string
     * @access protected
     */
    protected function getScope(/*# string */ $id)/*# : string */
    {
        if (isset($this->services[$id]['scope'])) {
            return $this->services[$id]['scope'];
        } else {
            return DefinitionAwareInterface::SCOPE_SHARED;
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
            if (false === strpos($name, '.')) {
                if (is_array($value)) {
                    $this->fixParameters($data[$name]);
                }
            } else {
                unset($data[$name]);
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
                        if (is_array($value)) {
                            $this->fixParameters($result[$part]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Normalize service definitions
     *
     * @param  array &$definitions
     * @return void
     * @throws LogicException if something goes wrong
     * @access protected
     */
    protected function fixServicesOld(array &$definitions)
    {
        foreach ($definitions as $id => $def) {
            // classname or closure
            if (is_string($def) || is_callable($def)) {
                $definitions[$id] = [ 'class' => [ $def ]];
                continue;
            }

            // error
            if (!is_array($def) || !isset($def['class']) && !isset($def[0])) {
                throw new LogicException(
                    Message::get(Message::DEFINITION_FORMAT_ERR, $id),
                    Message::DEFINITION_FORMAT_ERR
                );

            // fix no 'class'
            } else {
                if (isset($def[0])) {
                    $definitions[$id] = [ 'class' => $def ];
                } elseif (is_string($def['class'])) {
                    $def['class'] = [ $def['class'] ];
                    $definitions[$id] = $def;
                }
            }
        }
    }

    /**
     * Normalize service definitions
     *
     * @param  array &$definitions
     * @return void
     * @throws LogicException if something goes wrong
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
}
