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

namespace Phossa\Di;

use Phossa\Di\Message\Message;
use Phossa\Di\Exception\LogicException;
use Phossa\Di\Exception\NotFoundException;

/**
 * Container
 *
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     ContainerInterface
 * @version 1.0.6
 * @since   1.0.1 added
 */
class Container implements ContainerInterface
{
    use Factory\ServiceFactoryTrait;

    /**
     * current id cache for circular detection
     *
     * @var    array
     * @access protected
     */
    protected $circular = [];

    /**
     * service object counter
     *
     * @var    int
     * @access protected
     */
    protected $counter  = 0;

    /**
     * services pool
     *
     * @var    object[]
     * @access protected
     */
    protected $pool     = [];

    /**
     * Constructor
     *
     * Inject definitions and providers
     *
     * @param  array|string $definitionArrayOrFile array or a filename
     * @param  array $definitionProviders provider objects or classnames
     * @throws LogicException if something goes wrong
     * @access public
     * @api
     */
    public function __construct(
        $definitionArrayOrFile = '',
        array $definitionProviders = []
    ) {
        // load definitions from array or file
        if (!empty($definitionArrayOrFile)) {
            $this->load($definitionArrayOrFile);
        }

        // add definition providers
        if (count($definitionProviders)) {
            foreach ($definitionProviders as $provider) {
                $this->addProvider($provider);
            }
        }
    }

    /**
     * Accept second parameter $constructorArguments (array)
     * Accept third  parameter $inThisScope (string)
     *
     * {@inheritDoc}
     */
    public function get($id)
    {
        if ($this->has($id)) {

            // prepare constructor arguments and scope
            list($args, $scope, $sid) = $this->prepareArguments(
                $id,
                func_get_args()
            );

            // try get from pool first
            if (empty($args) && isset($this->pool[$sid])) {
                // get service from the pool
                return $this->pool[$sid];

            // not in pool, create the service
            } else {
                // circular detection
                $this->checkCircularMark($id);

                // create service base on definition
                $service = $this->serviceFromDefinition($id, $args);

                // remove circular detection mark
                $this->removeCircularMark($id);

                // store service in the pool
                if (static::SCOPE_SINGLE !== $scope) {
                    $this->pool[$sid] = $service;
                }

                return $service;
            }
        } else {
            throw new NotFoundException(
                Message::get(Message::SERVICE_ID_NOT_FOUND, $id),
                Message::SERVICE_ID_NOT_FOUND
            );
        }
    }

    /**
     * Accept second parameter $withAutowiring (bool)
     *
     * Non-string $id will return FALSE
     *
     * @inheritDoc
     */
    public function has($id)
    {
        // second argument
        $withAutowiring = func_num_args() > 1 ? (bool) func_get_arg(1) :
            $this->autowiring;

        // return FALSE if $id not a string
        return is_string($id) && (
            // found in definitions ?
            isset($this->services[$id])  ||

            // Or in definition providers ?
            $this->hasInProvider($id) ||

            // Or with id autowired ?
            $this->autoWiringId($id, $withAutowiring)
        ) ? true : false;
    }

    /**
     * @inheritDoc
     */
    public function one(
        /*# string */ $id,
        array $constructorArguments = []
    ) {
        return $this->get($id, $constructorArguments, self::SCOPE_SINGLE);
    }

    /**
     * @inheritDoc
     */
    public function run($callable, array $callableArguments = [])
    {
        return $this->executeCallable($callable, $callableArguments);
    }

    /**
     * Process arguments for get()
     *
     * @param  string $id service id
     * @param  array $arguments of get()
     * @return array
     * @access protected
     */
    protected function prepareArguments(/*# string */ $id, array $arguments)
    {
        $args  = isset($arguments[1]) ? (array) $arguments[1] : [];
        $scope = isset($arguments[2]) ? (string) $arguments[2] :
            $this->getScope($id);

        // scope === '@serviceId@' ?
        if (isset($this->circular[$scope])) {
            $scope .= '#' . $this->circular[$scope];
        }

        return [ $args, $scope, $scope . '::' . $id ];
    }

    /**
     * Check circular, create service and decorate service
     *
     * @param  string $id service id
     * @param  array $args constructor arguments
     * @return object
     * @access protected
     */
    protected function serviceFromDefinition($id, $args)
    {
        // create the service object
        $service = $this->createServiceObject($id, $args);

        // run predefined methods if any
        $this->runDefinedMethods($id, $service);

        // decorate the service if DecorateExtension loaded
        $this->decorateService($service);

        return $service;
    }

    /**
     * Check circular for get()
     *
     * @param  string $id service id
     * @return void
     * @throws LogicException if circular found
     * @access protected
     */
    protected function checkCircularMark(/*# string */ $id)
    {
        // reference id "@$id@" of current service object
        $refId = $this->getServiceReferenceId($id);

        // circular detected
        if (isset($this->circular[$refId])) {
            throw new LogicException(
                Message::get(Message::SERVICE_CIRCULAR, $id),
                Message::SERVICE_CIRCULAR
            );

        // mark it
        } else {
            $this->circular[$refId] = ++$this->counter;
        }
    }

    /**
     * Remove circular mark for get()
     *
     * @param  string $id service id
     * @return void
     * @access protected
     */
    protected function removeCircularMark(/*# string */ $id)
    {
        unset($this->circular[$this->getServiceReferenceId($id)]);
    }
}
