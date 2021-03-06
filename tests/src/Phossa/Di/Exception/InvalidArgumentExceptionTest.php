<?php

namespace Phossa\Di\Exception;

use Phossa\Di\Interop\InteropContainerException;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-02-20 at 19:20:19.
 */
class InvalidArgumentExceptionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var InvalidArgumentException
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new InvalidArgumentException;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @covers Phossa\Di\Exception\InvalidArgumentException::__construct
     */
    public function testConstruct()
    {
        $this->assertTrue($this->object instanceof InvalidArgumentException);
        $this->assertTrue($this->object instanceof ExceptionInterface);
        $this->assertTrue($this->object instanceof InteropContainerException);
        $this->assertTrue($this->object instanceof \Interop\Container\Exception\ContainerException);
    }

}
