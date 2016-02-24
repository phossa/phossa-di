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

namespace Phossa\Di\Extension\Loader;

use Phossa\Di\Extension\ExtensionAbstract;
use Phossa\Di\Message\Message;
use Phossa\Di\Exception\LogicException;
use Phossa\Di\Exception\NotFoundException;

/**
 * LoaderExtension
 *
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @see     ExtensionAbstract
 * @version 1.0.1
 * @since   1.0.1 added
 */
class LoaderExtension extends ExtensionAbstract
{
    /**
     * Extension class, has to be redefined in child classes
     *
     * @const
     */
    const EXTENSION_CLASS   = __CLASS__;

    /**
     * Load definitions from a file
     *
     * @param  string $file definition file name
     * @return array
     * @throws NotFoundException if file not found
     * @throws LogicException if something goes wrong
     * @access public
     * @api
     */
    public function loadFile(/*# string */ $file)/*# : array */
    {
        // check file
        $this->checkFile($file);

        // get type & loader class
        list($type, $class) = $this->getFileType($file);

        // get data
        $data = $class::loadFromFile($file);

        // supported types
        $types = [
            'm' => 'mappings',
            'p' => 'parameters',
            's' => 'services',
        ];

        if (isset($types[$type])) {
            return [ $types[$type] => $data ];
        } else {
            foreach ($types as $thetype) {
                if (isset($data[$thetype])) {
                    return $data;
                }
            }
            throw new LogicException(
                Message::get(Message::DEFINITION_FORMAT_ERR, $file),
                Message::DEFINITION_FORMAT_ERR
            );
        }
    }

    /**
     * Get file type and loader class
     *
     * Filename is in the format of XXX.(s|p|m).(php|xml|json);
     *
     * @param  string $file definition file name
     * @return string[]
     * @throws LogicException if something goes wrong
     * @access protected
     */
    protected function getFileType(/*# string */ $file)
    {
        $parts = explode('.', strtolower($file));
        $count = count($parts);
        if ($count < 2) {
            throw new LogicException(
                Message::get(Message::FILE_SUFFIX_UNKNOWN, $file),
                Message::FILE_SUFFIX_UNKNOWN
            );
        } elseif ($count < 3) {
            $type   = 'a';
            $suffix = $parts[1];
        } else {
            $type   = $parts[$count - 2][0];
            $suffix = $parts[$count - 1];
        }

        // loader class
        $class = __NAMESPACE__ . '\\Loader' . ucfirst($suffix);
        if (!class_exists($class)) {
            throw new LogicException(
                Message::get(Message::FILE_SUFFIX_UNKNOWN, $file),
                Message::FILE_SUFFIX_UNKNOWN
            );
        }

        return [ $type, $class ];
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
}
