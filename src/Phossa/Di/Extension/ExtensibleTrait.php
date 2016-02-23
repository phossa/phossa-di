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

namespace Phossa\Di\Extension;

use Phossa\Di\Message\Message;
use Phossa\Di\ContainerAwareInterface;
use Phossa\Di\Exception\LogicException;
use Phossa\Di\Extension\Loader\LoaderExtension;
use Phossa\Di\Extension\Provider\ProviderAbstract;
use Phossa\Di\Extension\Taggable\TaggableExtension;
use Phossa\Di\Extension\Provider\ProviderExtension;
use Phossa\Di\Extension\Delegate\DelegateExtension;
use Phossa\Di\Extension\Decorate\DecorateExtension;
use Phossa\Di\Extension\Delegate\DelegatorInterface;

/**
 * ExtensibleTrait
 *
 * Implementation of ExtensibleInterface
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     ExtensibleInterface
 * @version 1.0.1
 * @since   1.0.1 added
 */
trait ExtensibleTrait
{
    /**
     * extension registry
     *
     * @var    ExtensionInterface[]
     * @access protected
     */
    protected $extensions = [];

    /**
     * Cached provider after calling $container->has()
     *
     * @var    ProviderAbstract
     * @access protected
     */
    protected $provider;

    /**
     * {@inheritDoc}
     */
    public function addExtension(
        ExtensionInterface $extension
    )/*# : ExtensibleInterface */ {
        $this->extensions[$extension->getName()] = $extension;

        if ($extension instanceof ContainerAwareInterface) {
            $extension->setContainer($this);
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function load($fileOrArray)
    {
        $loaded = false;

        // load from file
        if (is_string($fileOrArray)) {
            $data = $this->getExtension(LoaderExtension::EXTENSION_NAME)
                         ->loadFile($fileOrArray);
            $this->load($data);
            return;

        // load from array
        } elseif (is_array($fileOrArray)) {
            if (isset($fileOrArray['services'])) {
                $this->add($fileOrArray['services']);
                $loaded = true;
            }

            if (isset($fileOrArray['parameters'])) {
                $this->set($fileOrArray['parameters']);
                $loaded = true;
            }

            if (isset($fileOrArray['mappings'])) {
                $this->map($fileOrArray['mappings']);
                $loaded = true;
            }
        }

        // not loaded
        if (!$loaded) {
            throw new LogicException(
                Message::get(
                    Message::DEFINITION_FORMAT_ERR,
                    gettype($fileOrArray)
                ),
                Message::DEFINITION_FORMAT_ERR
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setTags(array $tags)
    {
        $this->getExtension(TaggableExtension::EXTENSION_NAME)->setTags($tags);
    }

    /**
     * {@inheritDoc}
     */
    public function setDelegate(
        DelegatorInterface $delegator,
        /*# bool */ $keepAutowiring = false
    ) {
        $this->getExtension(DelegateExtension::EXTENSION_NAME)
             ->setDelegator($delegator, $this->auto($keepAutowiring));
    }

    /**
     * {@inheritDoc}
     */
    public function setDecorate(/*# string */ $name, $tester, $decorator)
    {
        $this->getExtension(DecorateExtension::EXTENSION_NAME)
             ->setDecorate($name, $tester, $decorator);
    }

    /**
     * {@inheritDoc}
     */
    public function addProvider($provider)
    {
        /* @var $ext ProviderExtension */
        $ext = $this->getExtension(ProviderExtension::EXTENSION_NAME);
        if ($provider instanceof ProviderAbstract) {
            $ext->addProvider($provider);
        } elseif (is_string($provider) && class_exists($provider)) {
            $ext->addProvider(new $provider);
        } else {
            throw new LogicException(
                Message::get(Message::EXT_PROVIDER_ERROR, $provider),
                Message::EXT_PROVIDER_ERROR
            );
        }
    }

    /**
     * Get the named extension, create one if injected yet
     *
     * @param  string $extensionName
     * @return ExtensionInterface
     * @access public
     */
    public function getExtension(
        /*# string */ $extensionName
    )/*# : ExtensionInterface */ {
        if (!$this->hasExtension($extensionName)) {
            $extClass = ExtensionMap::$mapping[$extensionName];
            $this->addExtension(new $extClass);
        }
        return $this->extensions[$extensionName];
    }

    /**
     * Has extension by the name ?
     *
     * @param  string $extensionName
     * @return bool
     * @access protected
     */
    protected function hasExtension(/*# string */ $extensionName)/*# : bool */
    {
        return isset($this->extensions[$extensionName]);
    }

    /**
     * Try find $id in providers, if matched, cache the provider
     *
     * @param  string $id
     * @return bool
     * @access protected
     */
    protected function hasInProvider(/*# string */ $id)/*# : bool */
    {
        $extName = ProviderExtension::EXTENSION_NAME;
        if ($this->hasExtension($extName)) {
            return $this->getExtension($extName)->providerHas($id);
        }
        return false;
    }

    /**
     * Decorate the service if DecorateExtension loaded
     *
     * @param  object $service
     * @return void
     * @throws LogicException if something goes wrong
     * @access protected
     */
    protected function decorateService($service)
    {
        $extName = DecorateExtension::EXTENSION_NAME;
        if ($this->hasExtension($extName)) {
            $this->getExtension($extName)->decorateService($service);
        }
    }
}
