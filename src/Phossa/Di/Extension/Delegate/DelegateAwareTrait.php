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

/**
 * DelegateAwareTrait
 *
 * Provides delegatedGet() and delegatedHas() methods
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.1
 * @since   1.0.1 added
 */
trait DelegateAwareTrait
{
    /**
     * Try get from delegator if DelegateExtension loaded
     *
     * @param  string $id
     * @return object
     * @throws NotFoundException
     * @access protected
     */
    protected function delegatedGet(/*# string */ $id)
    {
        $extName = DelegateExtension::EXTENSION_NAME;
        if ($this->hasExtension($extName)) {
            return $this->getExtension($extName)
                        ->getDelegator()
                        ->get($id);
        } else {
            /* @var $this ContainerInterface */
            return $this->get($id);
        }
    }

    /**
     * Try has from delegator if DelegateExtension loaded
     *
     * @param  string $id
     * @return bool
     * @throws NotFoundException
     * @access protected
     */
    protected function delegatedHas(/*# string */ $id)/*# : bool */
    {
        $extName = DelegateExtension::EXTENSION_NAME;
        if ($this->hasExtension($extName)) {
            return $this->getExtension($extName)
                        ->getDelegator()
                        ->has($id);
        } else {
            /* @var $this ContainerInterface */
            return $this->has($id);
        }
    }
}
