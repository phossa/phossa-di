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

namespace Phossa\Di\Definition\Scope;

/**
 * ScopeTrait
 *
 * Implementation of ScopeInterface
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     ScopeInterface
 * @version 1.0.4
 * @since   1.0.4 added
 */
trait ScopeTrait
{
    /**
     * default scope
     *
     * @var    string
     * @access protected
     */
    protected $default_scope = ScopeInterface::SCOPE_SHARED;

    /**
     * @inheritDoc
     */
    public function share(/*# bool */ $status = true)
    {
        $this->default_scope = $status ?
            ScopeInterface::SCOPE_SHARED :
            ScopeInterface::SCOPE_SINGLE ;
        return $this;
    }
}
