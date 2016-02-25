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

use Phossa\Di\ContainerInterface;
use Phossa\Di\Extension\ExtensionAbstract;

/**
 * DelegateExtension
 *
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     ExtensionAbstract
 * @version 1.0.4
 * @since   1.0.1 added
 */
class DelegateExtension extends ExtensionAbstract
{
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
     * @return void
     * @access public
     * @api
     */
    public function setDelegator(
        DelegatorInterface $delegator,
        ContainerInterface $container
    ) {
        $this->delegator = $delegator;
        $delegator->addContainer($container);
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
