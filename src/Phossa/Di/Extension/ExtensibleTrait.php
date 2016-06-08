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

use Phossa\Di\DelegatorInterface;
use Phossa\Di\Exception\LogicException;
use Phossa\Di\ContainerAwareInterface;
use Phossa\Di\Extension\Taggable\TaggableExtension;
use Phossa\Di\Extension\Provider\ProviderExtension;
use Phossa\Di\Extension\Delegate\DelegateExtension;
use Phossa\Di\Extension\Decorate\DecorateExtension;

/**
 * ExtensibleTrait
 *
 * Implementation of ExtensibleInterface
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     ExtensibleInterface
 * @version 1.0.6
 * @since   1.0.1 added
 */
trait ExtensibleTrait
{
    /**
     * cached extensions
     *
     * @var    ExtensionAbstract[]
     * @access protected
     */
    protected $extensions = [];

    /**
     * @inheritDoc
     */
    public function addExtension(ExtensionAbstract $extension)
    {
        $this->extensions[$extension->getName()] = $extension;

        // set container if ...
        if ($extension instanceof ContainerAwareInterface) {
            $extension->setContainer($this);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTag($tagOrTagArray)
    {
        /* @var $ext TaggableExtension */
        $ext = $this->getExtension(TaggableExtension::EXTENSION_CLASS);
        $ext->setTags(is_array($tagOrTagArray) ?
            $tagOrTagArray : [ (string) $tagOrTagArray ]
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasTag($tagOrTagArray)/*# : bool */
    {
        /* @var $ext TaggableExtension */
        $ext = $this->getExtension(TaggableExtension::EXTENSION_CLASS);
        return $ext->matchTags(is_array($tagOrTagArray) ?
            $tagOrTagArray : [ (string) $tagOrTagArray ]
        );
    }

    /**
     * @inheritDoc
     */
    public function setDelegate(DelegatorInterface $delegator)
    {
        /* @var $ext DelegateExtension */
        $ext = $this->getExtension(DelegateExtension::EXTENSION_CLASS);
        $ext->setDelegator($delegator);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addDecorate(
        /*# string */ $ruleName,
        $interfaceOrClosure,
        $decorateCallable
    ) {
        /* @var $ext DecorateExtension */
        $ext = $this->getExtension(DecorateExtension::EXTENSION_CLASS);
        $ext->setDecorate($ruleName, $interfaceOrClosure, $decorateCallable);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addProvider($providerOrClass)
    {
        /* @var $ext ProviderExtension */
        $ext = $this->getExtension(ProviderExtension::EXTENSION_CLASS);
        $ext->addProvider($providerOrClass);
        return $this;
    }

    /**
     * Get the named extension, create one if not injected yet
     *
     * @param  string $extensionName
     * @return ExtensionAbstract
     * @access public
     * @internal
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
     * Try find $id in providers
     *
     * @param  string $id
     * @return bool
     * @access protected
     */
    protected function hasInProvider(/*# string */ $id)/*# : bool */
    {
        $extName = ProviderExtension::EXTENSION_CLASS;

        // provider extension loaded
        if ($this->hasExtension($extName)) {
            /* @var $ext ProviderExtension */
            $ext = $this->getExtension($extName);
            return $ext->providerHas($id);
        }

        // extension not loaded yet
        return false;
    }

    /**
     * Decorate the service object if DecorateExtension loaded
     *
     * @param  object $service
     * @return self
     * @throws LogicException if something goes wrong
     * @access protected
     */
    protected function decorateService($service)
    {
        $extName = DecorateExtension::EXTENSION_CLASS;

        // decorate extension loaded
        if ($this->hasExtension($extName)) {
            /* @var $ext DecorateExtension */
            $ext = $this->getExtension($extName);
            $ext->decorateService($service);
        }

        return $this;
    }

    /**
     * Try get() from delegator if DelegateExtension loaded
     *
     * @param  string $id
     * @return bool|object
     * @throws NotFoundException
     * @access protected
     */
    protected function delegatedGet(/*# string */ $id)
    {
        $extName = DelegateExtension::EXTENSION_CLASS;
        if ($this->hasExtension($extName)) {
            /* @var $ext DelegateExtension */
            $ext = $this->getExtension($extName);
            return $ext->getDelegator()->get($id);
        } else {
            return $this->get($id);
        }
    }

    /*
     * for get() used in this trait
     */
    abstract public function get($id);
}
