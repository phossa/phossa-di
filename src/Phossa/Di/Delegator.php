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

namespace Phossa\Di;

use Phossa\Di\Message\Message;
use Phossa\Di\Exception\NotFoundException;
use Phossa\Di\Interop\InteropContainerInterface;
use Phossa\Di\Definition\Autowire\AutowiringInterface;

/**
 * Implementation of DelegatorInterface
 *
 * Auto wiring will be turned off for containers in the pool, except for the
 * last container which will be kept as it is
 *
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     DelegatorInterface
 * @version 1.0.6
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
        foreach ($this->containers as $idx => $con) {
            // remove container if added before (adjust location)
            if ($con === $container) {
                unset($this->containers[$idx]);

            // turnoff autowiring for other containers
            } elseif ($con instanceof AutowiringInterface) {
                $con->auto(false);
            }
        }

        // append container to the pool end
        $this->containers[] = $container;

        return $this;
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
        /* @var $container InteropContainerInterface */
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
     * {@inheritDoc}
     */
    public function has($id)
    {
        /* @var $container InteropContainerInterface */
        foreach ($this->getContainers() as $container) {
            if ($container->has($id)) {
                return true;
            }
        }
        return false;
    }
}
