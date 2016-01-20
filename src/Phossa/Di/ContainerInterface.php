<?php
/*
 * Phossa Project
 *
 * @see         http://www.phossa.com/
 * @copyright   Copyright (c) 2015 phossa.com
 * @license     http://mit-license.org/ MIT License
 */
/*# declare(strict_types=1); */

namespace Phossa\Di;

/**
 * ContainerInterface
 *
 * Borrowed from fig-standards/proposed/container.md
 * 
 * @interface
 * @package \Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface ContainerInterface
{
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param  string $id Identifier of the entry to look for.
     * @return mixed Entry.
     * @throws \Phossa\Di\Exception\NotFoundException
     *         No entry was found for this identifier.
     * @throws \Phossa\Di\Exception\ContainerException
     *         Error while retrieving the entry.
     * @access public
     * @api
     */
    public function get(/*# string */ $id);

    /**
     * Returns true if the container can return an entry for the given
     * identifier. Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw
     * an exception. It does however mean that `get($id)` will not throw a
     * `NotFoundException`.
     *
     * @param  string $id Identifier of the entry to look for.
     * @return bool
     */
    public function has(/*# string */ $id)/*# : bool */;
}
