<?php

namespace Phossa\Di\Factory;

use Phossa\Di\Definition\Reference\ParameterReference;
use Phossa\Di\Definition\Reference\ServiceReference;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-02-20 at 19:20:11.
 */
class DereferenceTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        include_once dirname(__DIR__) . '/testData5.php';
        $this->object = new \Phossa\Di\Container;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * Call protected/private method of a class.
     *
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    protected function invokeMethod($methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass($this->object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($this->object, $parameters);
    }

    /**
 	 * getPrivateProperty
 	 *
 	 * @param 	string $propertyName
 	 * @return	the property
 	 */
	public function getPrivateProperty($propertyName) {
		$reflector = new \ReflectionClass($this->object);
		$property  = $reflector->getProperty($propertyName);
		$property->setAccessible(true);

		return $property->getValue($this->object);
	}

    /**
     * @covers Phossa\Di\Factory\DereferenceTrait::isReference
     */
    public function testIsReference()
    {
        // normal parameter
        $ref1 = '%cache.test%';
        $this->assertEquals('cache.test',
            $this->invokeMethod('isReference',[$ref1])->getName()
        );

        // normal service
        $ref2 = '@cache@';
        $this->assertEquals('cache',
            $this->invokeMethod('isReference',[$ref2])->getName()
        );

        // wrong
        $ref3 = '@cache%';
        $this->assertEquals(false, $this->invokeMethod('isReference',[$ref3]));

        // bad
        $ref4 = '@@cache@@';
        $this->assertEquals('@cache@',
            $this->invokeMethod('isReference',[$ref4])->getName()
        );
    }

    /**
     * get parameter value, recursively
     *
     * @covers Phossa\Di\Factory\DereferenceTrait::getReferenceValue
     */
    public function testGetReferenceValue1()
    {
        // normal parameter
        $this->object->set('cache.test1', '/var/tmp');
        $ref1 = new ParameterReference('cache.test1');

        // test '%cache.test%' => '/var/tmp'
        $this->assertEquals('/var/tmp',
            $this->invokeMethod('getReferenceValue',[$ref1])
        );

        // test '%cache.test2%' => '%cache.test%' => '/var/tmp'
        $this->object->set('cache.test2', '%cache.test1%');
        $ref2 = new ParameterReference('cache.test2');
        $this->assertEquals('/var/tmp',
            $this->invokeMethod('getReferenceValue',[$ref2])
        );

        // test '%cache.test3%' => '%cache.test2%' =>
        // '%cache.test%' => '/var/tmp'
        $this->object->set('cache.test3', '%cache.test2%');
        $ref3 = new ParameterReference('cache.test3');
        $this->assertEquals('/var/tmp',
            $this->invokeMethod('getReferenceValue',[$ref3])
        );
    }

    /**
     * detect parameter loop
     *
     * @covers Phossa\Di\Factory\DereferenceTrait::getReferenceValue
     * @expectedException Phossa\Di\Exception\NotFoundException
     * @expectedExceptionCode Phossa\Di\Message\Message::PARAMETER_LOOP_FOUND
     */
    public function testGetReferenceValue2()
    {
        // normal parameter
        $this->object->set('cache.test1', '%cache.test2%');
        $this->object->set('cache.test2', '%cache.test1%');

        $ref1 = new ParameterReference('cache.test2');
        $this->assertEquals('/var/tmp',
            $this->invokeMethod('getReferenceValue',[$ref1])
        );
    }

    /**
     * get parameter => service refere
     *
     * @covers Phossa\Di\Factory\DereferenceTrait::getReferenceValue
     */
    public function testGetReferenceValue3()
    {
        include_once dirname(__DIR__) . '/testData1.php';

        // autowiring
        $aa = $this->object->get('AA');

        $this->object->set('cache.test1', '@AA@');
        $this->object->set('cache.test2', '%cache.test1%');
        $ref1 = new ParameterReference('cache.test2');

        // cache.test2 => %cache.test1% => @AA@
        $this->assertEquals($aa,
            $this->invokeMethod('getReferenceValue',[$ref1])
        );

        // @BB@
        $ref2 = new ServiceReference('BB');
        $this->assertTrue($aa->getB() ===
            $this->invokeMethod('getReferenceValue', [$ref2]));
    }

    /**
     * Test dereferenceArray
     *
     * @covers Phossa\Di\Factory\DereferenceTrait::dereferenceArray
     */
    /*
    public function testDereferenceArray()
    {
        include_once dirname(__DIR__) . '/testData1.php';

        // autowiring
        $aa = $this->object->get('AA');

        // normal parameter
        $this->object->set('cache.dir', '/var/tmp');

        // parameter => parameter => service
        $this->object->set('cache.test1', '@AA@');
        $this->object->set('cache.test2', '%cache.test1%');

        // dereference array recursively
        $res = [['%cache.dir%', 'wow', '%cache.test2%', '@BB@', [
            '@CC@', '%cache.dir%', [ 'bingo ']
        ]]];
        $this->invokeMethod('dereferenceArray', $res);

        $this->assertEquals('/var/tmp', $res[0][0]);
        $this->assertEquals('wow', $res[0][1]);
        $this->assertTrue($aa === $res[0][2]);
        $this->assertTrue($this->object->get('BB') === $res[0][3]);
        $this->assertTrue($this->object->get('CC') === $res[0][4][0]);
    }
     *
     */
}
