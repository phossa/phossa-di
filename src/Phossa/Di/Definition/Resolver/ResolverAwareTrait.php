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

namespace Phossa\Di\Definition\Resolver;

use Phossa\Di\Message\Message;
use Phossa\Config\ParameterInterface;
use Phossa\Di\Exception\NotFoundException;

/**
 * ResolverAwareTrait
 *
 * Implementation of ResolverAwareInterface
 *
 * @trait
 * @package Phossa\Di
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.7
 * @since   1.0.7 added
 */
trait ResolverAwareTrait
{
    /**
     * External config as parameter resolver
     *
     * @var    ParameterInterface
     * @access protected
     */
    protected $resolver;

    /**
     * default parameter pattern
     *
     * @var    array
     * @access protected
     */
    protected $paramter_pattern;

    /**
     * {@inheritDoc}
     */
    public function setResolver(ParameterInterface $resolver)
    {
        $this->resolver = $resolver;
        $this->paramter_pattern = $resolver->getReferencePattern();
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return ParameterInterface
     */
    public function getResolver()/*# : ParameterInterface */
    {
        if (!$this->hasResolver()) {
            throw new NotFoundException(
                Message::get(Message::PARAM_RESOLVER_MISS),
                Message::PARAM_RESOLVER_MISS
            );
        }
        return $this->resolver;
    }

    /**
     * {@inheritDoc}
     */
    public function hasResolver()/*# : bool */
    {
        return null !== $this->resolver;
    }
}
