<?php

namespace Phossa\Di\Definition;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-02-20 at 19:20:18.
 */
class ParameterReferenceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ParameterReference
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ParameterReference('parameter');
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
     * @covers Phossa\Di\Definition\ParameterReference::getName
     */
    public function testGetName()
    {
        $this->assertTrue($this->object instanceof ParameterReference);
        $this->assertTrue('parameter' === $this->object->getName());
    }
}
