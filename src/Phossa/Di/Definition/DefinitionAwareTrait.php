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
 * @version 1.0.7
 * @since   1.0.1 added
 * @since   1.0.7 added resolver support
 */
trait DefinitionAwareTrait
{
    use Scope\ScopeTrait,
        Loader\LoadableTrait,
        Autowire\AutowiringTrait,
        Reference\ReferenceActionTrait;

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
     * interface to class OR interface/class to id mapping
     *
     * @var    array
     * @access protected
     */
    protected $mappings     = [];

    /**
     * last added service id after add() called
     *
     * @var    string
     * @access protected
     */
    protected $last_added   = '';

    /**
     * @inheritDoc
     */
    public function set($nameOrArray, $valueStringOrArray = '')
    {
        // input is associate array, batch mode
        if (is_array($nameOrArray)) {
            // fix parameters
            $this->fixParameters($nameOrArray);

            // merge/replace with existing ones
            if ($this->hasResolver()) {
                $this->getResolver()->set($nameOrArray);
            } else {
                $this->parameters = array_replace_recursive(
                    $this->parameters,
                    $nameOrArray
                );
            }

        // name is string
        } elseif (is_string($nameOrArray)) {
            // value can be string or associate array
            $this->set([$nameOrArray => $valueStringOrArray]);

        // unknown type
        } else {
            throw new InvalidArgumentException(
                Message::get(
                    Message::PARAMETER_ID_INVALID,
                    gettype($nameOrArray)),
                Message::PARAMETER_ID_INVALID
            );
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function add(
        $id,
        $classOrClosure = '',
        array $constructorArguments = []
    ) {
        // flush last added service id
        $this->last_added = '';

        // add service definitions in batch mode
        if (is_array($id)) {
            // fix service definitions
            $this->fixServices($id);

            // add/overwrite existing ones
            $this->services = array_replace($this->services, $id);

        // add one service definiton
        } elseif (is_string($id)) {
            // missing classname, set to same as $id
            if ('' === $classOrClosure) {
                $classOrClosure = $id;
            }

            // register this service
            $this->services[$id] = ['class' => [
                $classOrClosure,
                $constructorArguments
            ]];

            // remember last added sevice id
            $this->last_added = $id;

        // unknown $id type
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
    public function map($nameOrArray, /*# string */ $toName = '')
    {
        // batch mode
        if (is_array($nameOrArray)) {
            $this->mappings = array_replace($this->mappings, $nameOrArray);

        // add one mapping
        } elseif (is_string($nameOrArray)) {
            $this->mappings[$nameOrArray] = (string) $toName;

        // unknown input
        } else {
            throw new InvalidArgumentException(
                Message::get(Message::MAP_ID_INVALID, gettype($nameOrArray)),
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
            return $this->load($this->loadDefinitionFromFile($fileOrArray));

        // load from array
        } elseif (is_array($fileOrArray)) {
            $toload = [
                'services'      => 'add',
                'parameters'    => 'set',
                'mappings'      => 'map'
            ];
            foreach ($toload as $key => $action) {
                if (isset($fileOrArray[$key])) {
                    $this->$action($fileOrArray[$key]);
                    $loaded = true;
                }
            }
        }

        // failure
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
    public function dump(/*# bool */ $toScreen = true)
    {
        $todo = [ 'services', 'parameters', 'mappings' ];
        $out  = $toScreen ? true : '';
        foreach ($todo as $section) {
            if ('parameters' === $section && $this->hasResolver()) {
                $data = $this->getResolver()->get(null);
            } else {
                $data = $this->$section;
            }
            if ($toScreen) {
                print_r($data, false);
            } else {
                $out .= print_r($data, true) . "\n";
            }
        }
        return $out;
    }

    /**
     * @inheritDoc
     */
    public function addMethod(
        /*# string */ $methodName,
        array $methodArguments = []
    ) {
        // check last added service id
        if (empty($this->last_added)) {
            throw new NotFoundException(
                Message::get(Message::SERVICE_ID_NOT_FOUND, __METHOD__),
                Message::SERVICE_ID_NOT_FOUND
            );

        // add the specific service definition
        } else {
            $service = &$this->services[$this->last_added];
            if (!isset($service['methods'])) {
                $service['methods'] = [];
            }
            $service['methods'][] = [ $methodName, $methodArguments ];
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setScope(/*# string */ $scope)
    {
        // check last added service id
        if (empty($this->last_added)) {
            throw new NotFoundException(
                Message::get(Message::SERVICE_ID_NOT_FOUND, __METHOD__),
                Message::SERVICE_ID_NOT_FOUND
            );

        // set scope for this service explicitly
        } else {
            $this->services[$this->last_added]['scope'] = (string) $scope;
        }
        return $this;
    }

    /**
     * Get the scope value for service $id
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
            return $this->services[$id]['scope'];
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
        if ($this->hasResolver()) {
            $found = $this->getResolver()->get($name);
        } else {
            // break into parts by '.'
            $parts = explode('.', $name);
            $found = $this->parameters;
            while (null !== ($part = array_shift($parts))) {
                if (!isset($found[$part])) {
                    $found = null;
                    break;
                }
                $found = $found[$part];
            }
        }

        if (null === $found) {
            throw new NotFoundException(
                Message::get(Message::PARAMETER_NOT_FOUND, $name),
                Message::PARAMETER_NOT_FOUND
                );
        }

        return $found;
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
     * Add service $id if $withAutowiring is true and $id is a existing class
     *
     * @param  string $id
     * @param  bool $withAutowiring
     * @return bool
     * @access protected
     */
    protected function autoWiringId(
        /*# string */ $id,
        /*# bool */ $withAutowiring
    )/*# : bool */ {
        if ($withAutowiring && class_exists($id)) {
            $this->add($id);
            return true;
        }
        return false;
    }
}
