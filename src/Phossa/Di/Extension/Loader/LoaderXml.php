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

use Phossa\Di\Message\Message;
use Phossa\Di\Exception\LogicException;

/**
 * LoaderPhp
 *
 * Load service/parameter definitions from files in XML format
 *
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.4
 * @since   1.0.1 added
 */
class LoaderXml implements LoaderInterface
{
    /**
     * @inheritDoc
     */
    public static function loadFromFile(/*# string */ $file)/*# : array */
    {
        if (false === ($xml = @simplexml_load_file($file))) {
            throw new LogicException(
                Message::get(Message::DEFINITION_FORMAT_ERR, $file),
                Message::DEFINITION_FORMAT_ERR
            );
        }
        return static::xmlToArray($xml);
    }

    /**
     * Convert SimpleXML object to array
     *
     * @param  \SimpleXMLElement $xml the xml object
     * @return array
     * @access protected
     * @static
     */
    protected static function xmlToArray(\SimpleXMLElement $xml)/*# : array */
    {
        $arr = array();
        foreach ($xml as $element) {
            $tag = $element->getName();
            $ele = get_object_vars($element);
            if (!empty($ele)) {
                $arr[$tag] = $element instanceof \SimpleXMLElement ?
                    static::xmlToArray($element) :
                    $ele;
            } else {
                $arr[$tag] = trim($element);
            }
        }
        return $arr;
    }
}
