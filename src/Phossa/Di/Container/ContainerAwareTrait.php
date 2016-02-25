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

namespace Phossa\Di\Container;

use Phossa\Di\Message\Message;
use Phossa\Di\ContainerInterface;
use Phossa\Di\Exception\NotFoundException;

/**
 * ContainerAwareTrait
 *
 * Implementation of ContainerAwareInterface
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     ContainerAwareInterface
 * @version 1.0.4
 * @since   1.0.1 added
 */
trait ContainerAwareTrait
{
    /**
     * the container
     *
     * @var    ContainerInterface
     * @access protected
     */
    protected $container;

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContainer()/*# : ContainerInterface */
    {
        // found
        if ($this->hasContainer()) {
            return $this->container;
        }

        // not found
        throw new NotFoundException(
            Message::get(Message::CONTAINER_NOT_FOUND, get_class($this)),
            Message::CONTAINER_NOT_FOUND
        );
    }

    /**
     * @inheritDoc
     */
    public function hasContainer()/*# : bool */
    {
        return is_null($this->container) ? false : true;
    }
}
