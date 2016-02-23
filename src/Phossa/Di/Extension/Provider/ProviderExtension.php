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
use Phossa\Di\ContainerAwareInterface;
use Phossa\Di\Exception\LogicException;
use Phossa\Di\Extension\ExtensionAbstract;

/**
 * ProviderExtension
 *
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     ExtensionAbstract
 * @version 1.0.1
 * @since   1.0.1 added
 */
class ProviderExtension extends ExtensionAbstract implements
    ContainerAwareInterface
{
    use \Phossa\Di\ContainerAwareTrait;

    /**
     * extension name
     */
    const EXTENSION_NAME    = 'provider';

    /**
     * Extension class, has to be redefined in child classes
     *
     * @const
     */
    const EXTENSION_CLASS   = __CLASS__;

    /**
     * provider registry
     *
     * @var    ProviderInterface[]
     * @access protected
     */
    protected $providers    = [];

    /**
     * Add provider to the registry
     *
     * @param  ProviderInterface $provider provider to add
     * @return void
     * @throws LogicException if merging goes wrong
     * @access public
     * @api
     */
    public function addProvider(ProviderInterface $provider)
    {
        // added already
        if ($provider->hasContainer()) {
            throw new LogicException(
                Message::get(
                    Message::EXT_PROVIDER_DUPPED,
                    get_class($provider)
                ),
                Message::EXT_PROVIDER_DUPPED
            );
        }

        // set container
        $provider->setContainer($this->getContainer());

        // provider works ?
        if ($provider->isProviding()) {
            if ($provider instanceof EagerProviderInterface) {
                // eager provider
                $provider->merge();
            } else {
                // lazy provider
                $this->providers[] = $provider;
            }
        }
    }

    /**
     * Get service/entry from provider
     *
     * @param  string $id service id
     * @return bool
     * @access public
     * @api
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
}
