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
     * Constructor, check $this->provides set or not
     *
     * @throws LogicException if something goes wrong
     * @access public
     * @final
     * @internal
     */
    final public function __construct()
    {
        // provides[] should be defined !
        if (!is_array($this->provides)) {
            throw new LogicException(
                Message::get(Message::EXT_PROVIDER_ERROR, get_class($this)),
                Message::EXT_PROVIDER_ERROR
            );
        }
    }

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
        if (in_array($id, $this->provides) && $this->isMatching()) {
            $this->merge();
            $this->provides = [];
            return true;
        }
        return false;
    }

    /**
     * Check tag matches
     *
     * Empty $this->tags always return TRUE !
     *
     * @return bool
     * @access protected
     */
    protected function isMatching()/*# : bool */
    {
        if (empty($this->tags)) {
            return true;
        } else {
            return $this->getContainer()->hasTag($this->tags);
        }
    }

    /**
     * Child class implements this method to merge defintions with container
     *
     * @return void
     * @throws LogicException if merging goes wrong
     * @access protected
     * @abstract
     */
    abstract protected function merge();
}
