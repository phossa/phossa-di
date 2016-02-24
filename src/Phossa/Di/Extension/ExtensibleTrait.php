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
    use \Phossa\Di\Definition\DefinitionAwareTrait;

    /**
     * extension registry
     *
     * @var    ExtensionAbstract[]
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
        ExtensionAbstract $extension
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
    public function load($fileOrArray)/*# : LoadableInterface */
    {
        $loaded = false;

        // load from file
        if (is_string($fileOrArray)) {
            /* @var $ext LoaderExtension */
            $ext = $this->getExtension(LoaderExtension::EXTENSION_CLASS);
            $data = $ext->loadFile($fileOrArray);
            return $this->load($data);

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

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addTag($tags)/*# : TaggableInterface */
    {
        /* @var $ext TaggableExtension */
        $ext = $this->getExtension(TaggableExtension::EXTENSION_CLASS);
        $ext->setTags(is_array($tags) ? $tags : [ $tags ]);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasTag($tags)/*# : bool */
    {
        // no tags found
        if (empty($tags)) return false;

        /* @var $ext TaggableExtension */
        $ext = $this->getExtension(TaggableExtension::EXTENSION_CLASS);
        return $ext->matchTags(is_array($tags) ? $tags : [ $tags ]);
    }

    /**
     * {@inheritDoc}
     */
    public function setDelegate(
        DelegatorInterface $delegator,
        /*# bool */ $keepAutowiring = false
    )/*# : DelegateAwareInterface */ {
        /* @var $ext DelegateExtension */
        $ext = $this->getExtension(DelegateExtension::EXTENSION_CLASS);
        $ext->setDelegator($delegator, $this->auto($keepAutowiring));
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setDecorate(/*# string */ $name, $tester, $decorator)
    {
        /* @var $ext DecorateExtension */
        $ext = $this->getExtension(DecorateExtension::EXTENSION_CLASS);
        $ext->setDecorate($name, $tester, $decorator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addProvider($provider)/*# : ProviderAwareInterface */
    {
        /* @var $ext ProviderExtension */
        $ext = $this->getExtension(ProviderExtension::EXTENSION_CLASS);

        if (is_a($provider, ProviderAbstract::PROVIDER_CLASS, true)) {
            if (is_object($provider)) {
                $ext->addProvider($provider);
            } else {
                $ext->addProvider(new $provider);
            }
        } else {
            throw new LogicException(
                Message::get(Message::EXT_PROVIDER_ERROR, $provider),
                Message::EXT_PROVIDER_ERROR
            );
        }

        return $this;
    }

    /**
     * Get the named extension, create one if injected yet
     *
     * @param  string $extensionName
     * @return ExtensionAbstract
     * @access public
     */
    public function getExtension(
        /*# string */ $extensionName
    )/*# : ExtensionAbstract */ {
        if (!$this->hasExtension($extensionName)) {
            $this->addExtension(new $extensionName);
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
        $extName = ProviderExtension::EXTENSION_CLASS;
        if ($this->hasExtension($extName)) {
            /* @var $ext ProviderExtension */
            $ext = $this->getExtension($extName);
            return $ext->providerHas($id);
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
        $extName = DecorateExtension::EXTENSION_CLASS;
        if ($this->hasExtension($extName)) {
            /* @var $ext DecorateExtension */
            $ext = $this->getExtension($extName);
            $ext->decorateService($service);
        }
    }

    /*
     * required by this trait
     */
    abstract public function auto(/*# bool */ $status);
}
