<?php

namespace Phossa\Di\Definition\Loader;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-02-20 at 19:20:13.
 */
class LoaderPhpTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var LoaderPhp
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {

    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * normal load
     *
     * @covers Phossa\Di\Definition\Loader\LoaderPhp::loadFromFile
     */
    public function testLoadFromFile1()
    {
        $this->assertEquals(
            ['cache' => [ 'bingo']], LoaderPhp::loadFromFile(__DIR__ . '/def1.php')
        );
    }

    /**
     * file not found
     *
     * @covers Phossa\Di\Definition\Loader\LoaderPhp::loadFromFile
     * @expectedException Phossa\Di\Exception\LogicException
     * @expectedExceptionCode Phossa\Di\Message\Message::DEFINITION_NOT_FOUND
     */
    public function testLoadFromFile2()
    {
        $this->assertEquals(
            ['cache' => [ 'bingo']], LoaderPhp::loadFromFile(__DIR__ . '/def2.php')
        );
    }

}