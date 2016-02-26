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
use Phossa\Di\Container\ContainerAwareInterface;
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
 * @version 1.0.4
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

        if ($extension instanceof ContainerAwareInterface) {
            $extension->setContainer($this);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTag($tags)
    {
        /* @var $ext TaggableExtension */
        $ext = $this->getExtension(TaggableExtension::EXTENSION_CLASS);
        $ext->setTags(is_array($tags) ? $tags : [ (string) $tags ]);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasTag($tags)/*# : bool */
    {
        /* @var $ext TaggableExtension */
        $ext = $this->getExtension(TaggableExtension::EXTENSION_CLASS);
        return $ext->matchTags(is_array($tags) ? $tags : [ (string) $tags ]);
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
    public function addDecorate(/*# string */ $name, $tester, $decorator)
    {
        /* @var $ext DecorateExtension */
        $ext = $this->getExtension(DecorateExtension::EXTENSION_CLASS);
        $ext->setDecorate($name, $tester, $decorator);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addProvider($provider)
    {
        /* @var $ext ProviderExtension */
        $ext = $this->getExtension(ProviderExtension::EXTENSION_CLASS);
        $ext->addProvider($provider);
        return $this;
    }

    /**
     * Get the named extension, create one if injected yet
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
}
