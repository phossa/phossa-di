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

namespace Phossa\Di\Extension\Provider;

use Phossa\Di\Message\Message;
use Phossa\Di\Exception\LogicException;
use Phossa\Di\Extension\ExtensionAbstract;
use Phossa\Di\Container\ContainerAwareInterface;

/**
 * ProviderExtension
 *
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     ExtensionAbstract
 * @version 1.0.4
 * @since   1.0.1 added
 */
class ProviderExtension extends ExtensionAbstract implements ContainerAwareInterface
{
    use \Phossa\Di\Container\ContainerAwareTrait;

    /**
     * @inheritDoc
     */
    const EXTENSION_CLASS = __CLASS__;

    /**
     * provider registry
     *
     * @var    ProviderAbstract[]
     * @access protected
     */
    protected $providers = [];

    /**
     * Add provider to the registry
     *
     * @param  ProviderAbstract|string $provider provider or classname
     * @return void
     * @access public
     * @internal
     */
    public function addProvider($provider)
    {
        $prov = $this->getProviderInstance($provider);
        $class = get_class($prov);
        if (isset($this->providers[$class])) {
            throw new LogicException(
                Message::get(
                    Message::EXT_PROVIDER_DUPPED,
                    get_class($prov)
                ),
                Message::EXT_PROVIDER_DUPPED
            );
        } else {
            $this->providers[$class] =
                $prov->setContainer($this->getContainer());
        }
    }

    /**
     * Get service/entry from provider
     *
     * @param  string $id service id
     * @return bool
     * @access public
     * @internal
     */
    public function providerHas(/*# string */ $id)/*# : bool */
    {
        foreach ($this->providers as $provider) {
            if ($provider->has($id)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the provider object
     *
     * @param  string|ProviderAbstract $provider class or object
     * @return ProviderAbstract
     * @throws LogicException
     * @access protected
     */
    public function getProviderInstance($provider)
    {
        if (is_a($provider, ProviderAbstract::PROVIDER_CLASS, true)) {
            if (!is_object($provider)) {
                $provider = new $provider;
            }
            return $provider;
        } else {
            throw new LogicException(
                Message::get(Message::EXT_PROVIDER_ERROR, $provider),
                Message::EXT_PROVIDER_ERROR
            );
        }
    }
}
