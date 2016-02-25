<?php
namespace Phossa\Di\Extension\Delegate;

use Phossa\Di\Container;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-02-21 at 19:01:49.
 */
class DelegatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Delegator
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Delegator;
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
     * @covers Phossa\Di\Extension\Delegate\Delegator::addContainer
     * @covers Phossa\Di\Extension\Delegate\Delegator::getContainers
     */
    public function testAddContainer()
    {
        // container one
        $ct1 = new Container();
        $this->object->addContainer($ct1);

        $all = $this->object->getContainers();
        $this->assertTrue(in_array($ct1, $all));
    }

    /**
     * Resolve dependencies thru delegator
     *
     * @covers Phossa\Di\Extension\Delegate\Delegator::get
     * @covers Phossa\Di\Extension\Delegate\Delegator::has
     */
    public function testGet1()
    {
        include_once dirname(dirname(__DIR__)) . '/testData1.php';

        // container one
        $ct1 = new Container();
        $aa1  = $ct1->get('AA'); // autowiring is ON

        // get from delegator
        $this->object->addContainer($ct1); // one-way
        $aa_1 = $this->object->get('AA');

        $this->assertTrue($aa1 === $aa_1);

        // container two
        $ct2 = new Container();
        $ct2->setDelegate($this->object); // two-ways

        // add service in $ct2
        $ct2->add('AA', 'AA');
        $aa2 = $ct2->get('AA');

        $this->assertFalse($aa1 === $aa2);

        // containers and delegator shares same $bb, all from $ct1
        $bb1 = $aa1->getB();
        $bb2 = $aa2->getB();
        $bb3 = $this->object->get('BB');

        $this->assertTrue($bb1 === $bb2);
        $this->assertTrue($bb1 === $bb3);
    }

    /**
     * Resolve dependencies thru delegator
     *
     * @covers Phossa\Di\Extension\Delegate\Delegator::get
     * @covers Phossa\Di\Extension\Delegate\Delegator::has
     */
    public function testGet2()
    {
        include_once dirname(dirname(__DIR__)) . '/testData1.php';
        include_once dirname(dirname(__DIR__)) . '/testData2.php';
        include_once dirname(dirname(__DIR__)) . '/testData3.php';

        $ct1 = new Container();
        $aa  = $ct1->get('AA'); // autowiring
        $ct1->setDelegate($this->object);

        $ct2 = new Container();
        $xaa = $ct2->get('XAA'); // autowiring
        $ct2->setDelegate($this->object);

        $ct3 = new Container();
        $ct3->setDelegate($this->object);
        $ct3->add('YAA', 'YAA');
        $ct3->add('YBB', 'YBB');

        // ct1 at first place
        $this->assertTrue($aa === $this->object->get('AA'));

        // ct2
        $this->assertTrue($xaa === $this->object->get('XAA'));

        // ct3
        $xcc = $this->object->get('YAA')->getC();
        $this->assertTrue($xcc === $this->object->get('XCC'));
    }

    /**
     * After set delegator, autowiring is turned off in container
     *
     * @covers Phossa\Di\Extension\Delegate\Delegator::get
     * @covers Phossa\Di\Extension\Delegate\Delegator::has
     * @expectedException Phossa\Di\Exception\NotFoundException
     * @expectedExceptionCode Phossa\Di\Message\Message::SERVICE_ID_NOT_FOUND
     */
    public function testGet3()
    {
        include_once dirname(dirname(__DIR__)) . '/testData1.php';

        $ct1 = new Container();
        $ct1->setDelegate($this->object);
        $this->object->get('AA'); // autowiring if OFF
    }
}
