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

namespace Phossa\Di\Definition\Loader;

use Phossa\Di\Message\Message;
use Phossa\Di\Exception\LogicException;
use Phossa\Di\Exception\NotFoundException;

/**
 * LoadableTrait
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.4
 * @since   1.0.1 added
 */
trait LoadableTrait
{
    /**
     * Load definitions from a file
     *
     * @param  string $file definition file name
     * @return array
     * @throws NotFoundException if file not found
     * @throws LogicException if something goes wrong
     * @access protected
     */
    protected function loadFile(/*# string */ $file)/*# : array */
    {
        $this->checkFile($file);
        list($type, $class) = $this->getFileType($file);
        $data = $class::loadFromFile($file);
        return $type ? [$type => $data] : $data;
    }

    /**
     * Get file type and loader class
     *
     * Filename is in the format of XXX.(s|p|m).(php|xml|json);
     *
     * @param  string $filename definition file name
     * @return string[]
     * @throws LogicException if something goes wrong
     * @access protected
     */
    protected function getFileType(/*# string */ $filename)
    {
        $parts  = explode('.', strtolower(basename($filename)));
        $count  = count($parts);
        $suffix = $count > 1 ? $parts[$count - 1] : 'php';
        $type   = $count > 2 ? $parts[$count - 2][0] : '_';

        // valid types
        $types  = ['s' => 'services', 'p' => 'parameters', 'm' => 'mappings'];

        return [
            isset($types[$type]) ? $types[$type] : false,
            $this->getLoaderClass($suffix, $filename)
        ];
    }

    /**
     * Check file exists and readable
     *
     * @param  string $file definition file name
     * @return void
     * @throws NotFoundException if not exist or readable
     * @access protected
     */
    protected function checkFile(/*# string */ $file)
    {
        if (!is_readable($file)) {
            throw new NotFoundException(
                Message::get(Message::DEFINITION_NOT_FOUND, $file),
                Message::DEFINITION_NOT_FOUND
            );
        }
    }

    /**
     * Get load class base on suffix
     *
     * @param  string $suffix
     * @param  string $filename
     * @return string classname
     * @throws NotFoundException if not class not found
     * @access protected
     */
    protected function getLoaderClass(
        /*# string */ $suffix,
        /*# string */ $filename
    ) {
        $class = __NAMESPACE__ . '\\Loader' . ucfirst($suffix);
        if (!class_exists($class)) {
            throw new LogicException(
                Message::get(Message::FILE_SUFFIX_UNKNOWN, $filename),
                Message::FILE_SUFFIX_UNKNOWN
            );
        }
        return $class;
    }
}
