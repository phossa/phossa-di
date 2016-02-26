<?php
namespace Phossa\Di\Extension\Delegate;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-02-21 at 19:01:50.
 */
class DelegateExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DelegateExtension
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new DelegateExtension;
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
     * @covers Phossa\Di\Extension\Delegate\DelegateExtension::setDelegator
     */
    public function testSetDelegator()
    {
        $delegator = new \Phossa\Di\Delegator;
        $container = new \Phossa\Di\Container;
        $this->object->setContainer($container);
        $this->object->setDelegator($delegator);

        $this->assertTrue($delegator === $this->getPrivateProperty('delegator'));
    }

    /**
     * @covers Phossa\Di\Extension\Delegate\DelegateExtension::getDelegator
     */
    public function testDelegatorHas()
    {
        include_once dirname(dirname(__DIR__)) . '/testData1.php';
        $container = new \Phossa\Di\Container;
        $aa = $container->get('AA');
        $this->object->setContainer($container);
        
        $delegator = new \Phossa\Di\Delegator;
        $this->object->setDelegator($delegator);

        $this->assertTrue($this->object->getDelegator()->get('AA') === $aa);
    }
}
