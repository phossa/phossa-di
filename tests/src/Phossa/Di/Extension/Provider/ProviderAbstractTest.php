<?php
namespace Phossa\Di\Extension\Provider;

use Phossa\Di\Exception\NotFoundException;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-02-21 at 09:41:00.
 */
class ProviderAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProviderAbstract
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        include_once dirname(dirname(__DIR__)) . '/testData1.php';

        include_once __DIR__ .'/TestProvider.php';
        include_once __DIR__ .'/TestTagProvider.php';

        $c = new \Phossa\Di\Container();
        $this->object_one = new TestTagProvider();
        $this->object_one->setContainer($c);

        $this->object_two = new TestProvider();
        $this->object_two->setContainer($c);
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
     * @covers Phossa\Di\Extension\Provider\ProviderAbstract::get
     */
    public function testGet()
    {
        // container
        $c = $this->object_one->getContainer();

        // switch off autowiring
        $c->auto(false);

        // merged into container and get the result
        $this->assertTrue($this->object_two->get('XX') instanceof \bingoXX);

        // has NO 'XX' in provider now
        $this->assertFalse($this->object_two->has('XX'));

        // tag not match, get() failed
        try {
            // get failed
            $this->object_one->get('bingo');
            $this->assertFalse(true);
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof NotFoundException);
        }

        // reset container tags
        $c->addTag(['TEST']);

        // now get() is good
        $this->assertTrue($this->object_one->get('bingo') instanceof \bingoXX);
    }

    /**
     * @covers Phossa\Di\Extension\Provider\ProviderAbstract::has
     */
    public function testHas()
    {
        // container
        $c = $this->object_one->getContainer();

        // switch off autowiring
        $c->auto(false);

        // not merged yet, has XX in provider
        $this->assertTrue($this->object_two->has('XX'));

        // merge
        $this->object_two->merge();

        // has NO 'XX' in provider now
        $this->assertFalse($this->object_two->has('XX'));

        // ALWAYS has NO 'bingo' in provider one because tag mismatch
        $this->assertFalse($this->object_one->has('bingo'));

        // merge failed because tag mismatch
        $this->object_one->merge();

        // ALWAYS has NO 'bingo' in provider one because tag mismatch
        $this->assertFalse($this->object_one->has('bingo'));

        // reset container tags
        $c->addTag(['TEST']);

        // has 'bingo' in provider one because tags match
        $this->assertTrue($this->object_one->has('bingo'));

        // merged
        $this->object_one->merge();

        // has NO 'bingo' in provider one after merge
        $this->assertFalse($this->object_one->has('bingo'));
    }

    /**
     * @covers Phossa\Di\Extension\Provider\ProviderAbstract::merge
     */
    public function testMerge()
    {
        // container
        $c = $this->object_one->getContainer();

        // switch off autowiring
        $c->auto(false);

        // has no 'XX' in container initially
        $this->assertFalse($c->has('XX'));

        // merge
        $this->object_two->merge();

        // has 'XX' in container now
        $this->assertTrue($c->has('XX'));

        // has no 'bingo' in container initially
        $this->assertFalse($c->has('bingo'));

        // merge failed because tag mismatch
        $this->object_one->merge();

        // still has no 'bingo' because tags mismatch
        $this->assertFalse($c->has('bingo'));
    }

    /**
     * @covers Phossa\Di\Extension\Provider\ProviderAbstract::isProviding
     */
    public function testIsProviding1()
    {
        //container has no tags
        $this->assertFalse($this->object_one->isProviding());
        $this->assertTrue($this->object_two->isProviding());

        // container with miss match tags
        $this->object_one->getContainer()->addTag(['WOW']);
        $this->assertFalse($this->object_one->isProviding());
        $this->assertTrue($this->object_two->isProviding());

        // container with matched tags
        $this->object_one->getContainer()->addTag(['TEST']);
        $this->assertTrue($this->object_one->isProviding());
        $this->assertTrue($this->object_two->isProviding());
    }
}
