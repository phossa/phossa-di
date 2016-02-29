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

namespace Phossa\Di\Extension\Decorate;

use Phossa\Di\ContainerAwareInterface;
use Phossa\Di\Exception\LogicException;
use Phossa\Di\Extension\ExtensionAbstract;

/**
 * DecorateExtension
 *
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     ExtensionAbstract
 * @version 1.0.6
 * @since   1.0.1 added
 */
class DecorateExtension extends ExtensionAbstract implements ContainerAwareInterface
{
    use \Phossa\Di\ContainerAwareTrait;

    /**
     * @inheritDoc
     */
    const EXTENSION_CLASS = __CLASS__;

    /**
     * Decorate rules
     *
     * @var    array
     * @access protected
     */
    protected $rules = [];

    /**
     * Apply decorating rules to a service object if matches
     *
     * @param  object $service
     * @return void
     * @throws LogicException if something goes wrong
     * @access public
     * @internal
     */
    public function decorateService($service)
    {
        try {
            foreach ($this->rules as $rule) {
                // if closure returns true
                if ($rule[0]($service)) {
                    // execute the decorate callable
                    $rule[1]($service);
                }
            }
        } catch (\Exception $e) {
            throw new LogicException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Set up docorating rules
     *
     * @param  string $ruleName decorate rule name
     * @param  string|callable $interfaceOrClosure callable or interface/class
     * @param  array|callable $decorateCallable callable or [method, arguments]
     * @return void
     * @throws LogicException if something goes wrong
     * @access public
     * @internal
     */
    public function setDecorate(
        /*# string */ $ruleName,
        $interfaceOrClosure,
        $decorateCallable
    ) {
        // create closure it it is a interface name
        if (!is_callable($interfaceOrClosure)) {
            $interfaceOrClosure = function ($service) use ($interfaceOrClosure) {
                    return $service instanceof $interfaceOrClosure;
                };
        }

        // create closure if array [method, arguments] provided
        if (!is_callable($decorateCallable)) {
            $method    = $decorateCallable[0];
            $container = $this->getContainer();
            $arguments = isset($decorateCallable[1]) ? $decorateCallable[1] : [];
            $decorateCallable = function ($service) use ($container, $method, $arguments) {
                    $container->run([$service, $method], $arguments);
                };
        }

        // set the rule
        $this->rules[$ruleName] = [ $interfaceOrClosure, $decorateCallable ];
    }
}
