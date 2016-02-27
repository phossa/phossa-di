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
 * @version 1.0.6
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
    protected function loadDefinitionFromFile(/*# string */ $file)/*# : array */
    {
        // check first
        $this->checkFileExistence($file);

        // get definition type and loader class base on suffix
        list($type, $class) = $this->getFileTypeClass($file);

        // load into an array
        $data = $class::loadFromFile($file);

        // associate with definition type
        return $type ? [$type => $data] : $data;
    }

    /**
     * Get definition type and loader class
     *
     * *.[php|json|xml]    : all definitions
     * *.s*.[php|json|xml] : service definitions
     * *.p*.[php|json|xml] : parameter definitions
     * *.m*.[php|json|xml] : mappings definitions
     *
     * @param  string $filename definition file name
     * @return string[]
     * @throws LogicException if something goes wrong
     * @access protected
     */
    protected function getFileTypeClass(/*# string */ $filename)
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
    protected function checkFileExistence(/*# string */ $file)
    {
        if (!file_exists($file) || !is_readable($file)) {
            throw new NotFoundException(
                Message::get(Message::DEFINITION_NOT_FOUND, $file),
                Message::DEFINITION_NOT_FOUND
            );
        }
    }

    /**
     * Get loader class name base on suffix
     *
     * @param  string $suffix
     * @param  string $filename
     * @return string classname
     * @throws LogicException if not class not found
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
