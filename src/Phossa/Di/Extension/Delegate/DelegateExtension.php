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

use Phossa\Di\DelegatorInterface;
use Phossa\Di\Extension\ExtensionAbstract;
use Phossa\Di\Container\ContainerAwareInterface;

/**
 * DelegateExtension
 *
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     ExtensionAbstract
 * @version 1.0.4
 * @since   1.0.1 added
 */
class DelegateExtension extends ExtensionAbstract implements ContainerAwareInterface
{
    use \Phossa\Di\Container\ContainerAwareTrait;

    /**
     * @inheritDoc
     */
    const EXTENSION_CLASS = __CLASS__;

    /**
     * delegator
     *
     * @var    DelegatorInterface
     * @access protected
     */
    protected $delegator;

    /**
     * Set delegator
     *
     * @param  DelegatorInterface $delegator
     * @return static
     * @access public
     * @api
     */
    public function setDelegator(DelegatorInterface $delegator)
    {
        $this->delegator = $delegator->addContainer($this->getContainer());
        return $this;
    }

    /**
     * Get the delegator ?
     *
     * @return DelegatorInterface
     * @access public
     * @api
     */
    public function getDelegator()/*# : DelegatorInterface */
    {
        return $this->delegator;
    }
}
