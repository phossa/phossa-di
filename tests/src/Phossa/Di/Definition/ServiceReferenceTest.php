<?php

namespace Phossa\Di\Definition;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-02-20 at 19:20:17.
 */
class ServiceReferenceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ServiceReference
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ServiceReference('service');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * normal test
     *
     * @covers Phossa\Di\Definition\ServiceReference::getName
     */
    public function testGetName()
    {
        $this->assertTrue($this->object instanceof ServiceReference);
        $this->assertTrue('service' === $this->object->getName());
    }
}
