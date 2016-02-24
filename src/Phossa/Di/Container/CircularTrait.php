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
use Phossa\Di\Exception\LogicException;

/**
 * CircularTrait
 *
 * Check service circular loops
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.1
 * @since   1.0.1 added
 */
trait CircularTrait
{
    /**
     * circular detection for get()
     *
     * @var    array
     * @access protected
     */
    protected $circular = [];

    /**
     * Check circular for get()
     *
     * @param  string $id service id
     * @param  string &$scope scope
     * @return void
     * @throws LogicException if circular found
     * @access protected
     */
    protected function checkCircular(/*# string */ $id, /*# string */ &$scope)
    {
        static $count = 0;

        // reference id "@$id@"
        $refId = $this->getReferenceId($id);

        // circular detection
        if (isset($this->circular[$refId])) {
            throw new LogicException(
                Message::get(Message::SERVICE_CIRCULAR, $id),
                Message::SERVICE_CIRCULAR
            );
        } else {
            $this->circular[$refId] = ++$count;

            // mark this object is shared under parent object
            if (isset($this->circular[$scope])) {
                $scope += '::' . $count;
            }
        }
    }

    /**
     * Remove circular mark for get()
     *
     * @param  string $id service id
     * @return void
     * @access protected
     */
    protected function removeCircular(/*# string */ $id)
    {
        unset($this->circular[$this->getReferenceId($id)]);
    }

    /**
     * Generate '@serviceId@'
     *
     * @param  string $id service id
     * @return string
     * @access protected
     */
    protected function getReferenceId(/*# string */ $id)/*# : string */
    {
        return '@' . $id . '@';
    }
}
