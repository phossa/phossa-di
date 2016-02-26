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
 * Scope constants
 *
 * @interface
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.4
 * @since   1.0.4 added
 */
interface ScopeInterface
{
    /**
     * Shared scope: share instance each time
     *
     * @var    string
     * @access public
     */
    const SCOPE_SHARED = '__SHARED__';

    /**
     * Single scope: create new instance each time
     *
     * @var    string
     * @access public
     */
    const SCOPE_SINGLE = '__SINGLE__';

    /**
     * Make container default scope to share or non-share
     *
     * @param  bool $status sharing status
     * @return static
     * @access public
     * @api
     */
    public function share(/*# bool */ $status = true);
}
