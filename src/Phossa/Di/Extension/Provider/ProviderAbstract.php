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
use Phossa\Di\Exception\NotFoundException;
use Phossa\Di\Interop\InteropContainerInterface;
use Phossa\Di\Container\ContainerAwareInterface;
use Phossa\Di\Extension\Taggable\TaggableExtension;

/**
 * ProviderAbstract
 *
 * @abstract
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.4
 * @since   1.0.1 added
 */
abstract class ProviderAbstract implements ContainerAwareInterface, InteropContainerInterface
{
    use \Phossa\Di\Container\ContainerAwareTrait;

    /**
     * class name
     *
     * @var    string[]
     */
    const PROVIDER_CLASS = __CLASS__;

    /**
     * Services we provide, CHILD CLASS HAS TO POPULATE THIS ARRAY,
     *
     * @var    string[]
     * @access protected
     */
    protected $provides;

    /**
     * Tags of the provider
     *
     * @var    string[]
     * @access protected
     */
    protected $tags = [];

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        if ($this->has($id)) {
            return $this->getContainer()->get($id);
        } else {
            throw new NotFoundException(
                Message::get(Message::SERVICE_ID_NOT_FOUND, $id),
                Message::SERVICE_ID_NOT_FOUND
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function has($id)
    {
        if ($this->isProviding() && in_array($id, $this->provides)) {
            // auto merge definitions
            $this->merge();

            // indicating found $id
            return true;
        }
        return false;
    }

    /**
     * Merge this provider's service definition into container
     *
     * @return void
     * @throws LogicException if merging goes wrong
     * @access public
     * @internal
     */
    public function merge()
    {
        // call user-defined merging method
        if ($this->isProviding()) {
            $this->mergeDefinition();

            // empty the provides after merge
            $this->provides = [];
        }
    }

    /**
     * Is this provider currently providing definitions ?
     *
     * @return bool
     * @access public
     * @internal
     */
    public function isProviding()/*# : bool */
    {
        // provides[] should be defined !
        if (!is_array($this->provides)) {
            throw new LogicException(
                Message::get(Message::EXT_PROVIDER_ERROR, get_class($this)),
                Message::EXT_PROVIDER_ERROR
            );
        }

        // empty ?
        if (empty($this->provides)) {
            return false;
        }

        // match tags with container
        return empty($this->tags) ? true :$this->matchContainerTags();
    }

    /**
     * Child class implements this method to merge defintions with container
     *
     * @return void
     * @throws LogicException if merging goes wrong
     * @access protected
     * @abstract
     */
    abstract protected function mergeDefinition();

    /**
     * Matching provider's tags with container's
     *
     * @return bool
     * @access protected
     */
    protected function matchContainerTags()/*# : bool */
    {
        /* @var $container Container */
        $container = $this->getContainer();

        /* @var $ext TaggableExtension */
        $ext = $container->getExtension(TaggableExtension::EXTENSION_CLASS);

        return $ext->matchTags($this->tags);
    }
}
