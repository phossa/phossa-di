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

namespace Phossa\Di\Extension\Delegate;

use Phossa\Di\Message\Message;
use Phossa\Di\Exception\NotFoundException;
use Phossa\Di\Interop\InteropContainerInterface;

/**
 * Implementation of DelegatorInterface
 *
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     DelegatorInterface
 * @version 1.0.4
 * @since   1.0.1 added
 */
class Delegator implements DelegatorInterface
{
    /**
     * containers
     *
     * @var    InteropContainerInterface[]
     * @access protected
     */
    protected $containers = [];

    /**
     * @inheritDoc
     */
    public function addContainer(InteropContainerInterface $container)
    {
        // remove duplicated $container
        foreach ($this->containers as $idx => $con) {
            if ($con === $container) {
                unset($this->containers[$idx]);
            }
        }

        // append to the end
        $this->containers[] = $container;
    }

    /**
     * @inheritDoc
     */
    public function getContainers()/*# : array */
    {
        return $this->containers;
    }

    /**
     * Get from the delegator
     *
     * {@inheritDoc}
     */
    public function get($id)
    {
        /* @var InteropContainerInterface $container */
        foreach ($this->getContainers() as $container) {
            if ($container->has($id)) {
                return $container->get($id);
            }
        }

        // not found
        throw new NotFoundException(
            Message::get(Message::SERVICE_ID_NOT_FOUND, $id),
            Message::SERVICE_ID_NOT_FOUND
        );
    }

    /**
     * Check each container in the delegator
     *
     * {@inheritDoc}
     */
    public function has($id)
    {
        /* @var InteropContainerInterface $container */
        foreach ($this->getContainers() as $container) {
            if ($container->has($id)) {
                return true;
            }
        }
        return false;
    }
}
