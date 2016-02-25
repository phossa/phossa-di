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

use Phossa\Di\Exception\LogicException;
use Phossa\Di\Extension\ExtensionAbstract;
use Phossa\Di\Container\ContainerAwareInterface;

/**
 * DecorateExtension
 *
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     ExtensionAbstract
 * @version 1.0.4
 * @since   1.0.1 added
 */
class DecorateExtension extends ExtensionAbstract implements ContainerAwareInterface
{
    use \Phossa\Di\Container\ContainerAwareTrait;

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
     * Decorate a service object
     *
     * @param  object $service
     * @return void
     * @throws LogicException if something goes wrong
     * @access public
     * @internal
     */
    public function decorateService($service)
    {
        foreach ($this->rules as $rule) {
            // tester
            if ($rule[0]($service)) {
                // decorator
                $rule[1]($service);
            }
        }
    }

    /**
     * Set docorate rules
     *
     * @param  string $name decorate rule name
     * @param  string|callable callable or interface/class name
     * @param  array|callable callable or [ method, arguments ]
     * @return void
     * @throws LogicException if something goes wrong
     * @access public
     * @internal
     */
    public function setDecorate(/*# string */ $name, $tester, $decorator)
    {
        // tester
        if (!is_callable($tester)) {
            $tester = function ($service) use ($tester) {
                return $service instanceof $tester;
            };
        }

        // decorator
        if (!is_callable($decorator)) {
            $method    = $decorator[0];
            $container = $this->getContainer();
            $arguments = isset($decorator[1]) ? $decorator[1] : [];
            $decorator = function ($service)
                use ($container, $method, $arguments)
                {
                    $container->run([$service, $method], $arguments);
                };
        }

        // set the rule
        $this->rules[$name] = [ $tester, $decorator ];
    }
}
