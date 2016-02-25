<?php

namespace Phossa\Di;

use Phossa\Di\Extension\Taggable\TaggableExtension;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-02-20 at 19:20:11.
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
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
        include_once __DIR__ . '/testData1.php';
        include_once __DIR__ . '/testData2.php';
        include_once __DIR__ . '/testData5.php';
        include_once __DIR__ . '/testData6.php';
        $this->object = new Container;
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
     * definition array format error
     *
     * @covers Phossa\Di\Container::load
     * @expectedException Phossa\Di\Exception\LogicException
     * @expectedExceptionCode Phossa\Di\Message\Message::DEFINITION_FORMAT_ERR
     */
    public function testLoad1()
    {
        // load parameters
        $p1 = [[ 'test.test' => 'one' ]];
        $this->object->load($p1);
    }

    /**
     * definition file missing
     *
     * @covers Phossa\Di\Container::load
     * @expectedException Phossa\Di\Exception\NotFoundException
     * @expectedExceptionCode Phossa\Di\Message\Message::DEFINITION_NOT_FOUND
     */
    public function testLoad2()
    {
        // load file
        $this->object->load('notsuchfile.php');
    }

    /**
     * load parameters from definition file
     *
     * @covers Phossa\Di\Container::load
     */
    public function testLoad3()
    {
        // load file
        $this->object->load(__DIR__.'/Extension/Loader/def1.param.php');
        $this->assertEquals(
            ['cache' => ['bingo']],
            $this->getPrivateProperty('parameters')
        );
    }

    /**
     * definition format error
     *
     * @covers Phossa\Di\Container::load
     * @expectedException Phossa\Di\Exception\LogicException
     * @expectedExceptionCode Phossa\Di\Message\Message::DEFINITION_FORMAT_ERR
     */
    public function testLoad4()
    {
        // load wrong format array
        $this->object->load([]);
    }

    /**
     * test load parameters from array
     *
     * @covers Phossa\Di\Container::load
     */
    public function testLoadParameters()
    {
        // load parameters
        $p1 = [ 'parameters' => [ 'test.test' => 'one']];
        $this->object->load($p1);

        $this->assertEquals(
            [ 'test' => 'one'], $this->invokeMethod('getParameter', ['test'])
        );

        $this->assertEquals(
            'one', $this->invokeMethod('getParameter', ['test.test'])
        );

        // merge parameters
        $p2 = [ 'parameters' => [ 'test.test2' => 'two']];
        $this->object->load($p2);

        $this->assertEquals(
            [ 'test' => 'one', 'test2' => 'two'],
            $this->invokeMethod('getParameter', ['test'])
        );

        // load array
        $p3 = [ 'parameters' => [ 'test' => [ 'test3' => 'three']]];
        $this->object->load($p3);

        $this->assertEquals(
            [ 'test' => 'one', 'test2' => 'two', 'test3' => 'three'],
            $this->invokeMethod('getParameter', ['test'])
        );

        // replace parameters
        $p4 = [ 'parameters' => [ 'test.test' => 'four']];
        $this->object->load($p4);

        $this->assertEquals(
            [ 'test' => 'four', 'test2' => 'two', 'test3' => 'three'],
            $this->invokeMethod('getParameter', ['test'])
        );

        $p5 = [ 'parameters' => [ 'test' =>
                [ 'test' => 'wow', 'test3' => 'wow2']]];
        $this->object->load($p5);

        $this->assertEquals(
            [ 'test' => 'wow', 'test2' => 'two', 'test3' => 'wow2'],
            $this->invokeMethod('getParameter', ['test'])
        );

        $p6 = [ 'parameters' => [ 'test' => 'six']];
        $this->object->load($p6);

        $this->assertEquals(
            'six', $this->invokeMethod('getParameter', ['test'])
        );
    }

    /**
     * test load services
     *
     * @covers Phossa\Di\Container::load
     */
    public function testLoadServices()
    {
        // init values
        $class = '\\Phossa\\Di\\Container';
        $call  = function() { return true; };

        // id => 'classname'
        $s1 = [ 'services' => [ 'container' => $class]];
        $this->object->load($s1);
        $o1 = $this->getPrivateProperty('services')['container'];
        $this->assertEquals(['class' => [$class]], $o1);

        // id => callable()
        $s2 = [ 'services' => [ 'container' => $call ]];
        $this->object->load($s2);
        $o2 = $this->getPrivateProperty('services')['container'];
        $this->assertTrue($o2['class'][0] instanceof \Closure);

        // id => [ 'classname', arguments[] ]
        $s3 = [ 'services' => [ 'container' => [$class, []]]];
        $this->object->load($s3);
        $o3 = $this->getPrivateProperty('services')['container'];
        $this->assertEquals(['class' => [$class, []]], $o3);

        // id => [ callable(), arguments[] ]
        $s4 = [ 'services' => [ 'container' => [$call, []]]];
        $this->object->load($s4);
        $o4 = $this->getPrivateProperty('services')['container'];
        $this->assertEquals(['class' => [$call, []]], $o4);

        // id => ['class' => [ 'classname', arguments[] ]];
        $s5 = [ 'services' => [ 'container' => ['class'=> [$class, []]]]];
        $this->object->load($s5);
        $o5 = $this->getPrivateProperty('services')['container'];
        $this->assertEquals(['class' => [$class, []]], $o5);

        // id => ['class' => [ callable(), arguments[] ]]
        $s6 = [ 'services' => [ 'container' => ['class' => [$call, [] ]]]];
        $this->object->load($s6);
        $o6 = $this->getPrivateProperty('services')['container'];
        $this->assertEquals(['class' => [$call, []]], $o6);
    }

    /**
     * test load mappings
     *
     * @covers Phossa\Di\Container::load
     */
    public function testLoadMappings()
    {
        // init values
        $s1 = [ 'mappings' => [ 'interface' => 'classname' ]];
        $this->object->load($s1);
        $o1 = $this->getPrivateProperty('mappings')['interface'];
        $this->assertEquals('classname', $o1);
    }

    /**
     * @covers Phossa\Di\Container::has
     */
    public function testHas1()
    {
        // turn off autowiring
        $this->object->auto(false);

        // must be string
        $this->assertFalse($this->object->has(['AA']));

        // not defined
        $this->assertFalse($this->object->has('WW'));

        // define it, and check again
        $this->object->add('WW', 'AA');
        $this->assertTrue($this->object->has('WW'));

        $this->assertFalse($this->object->has('BB'));

        // autowired, if BB is a class
        $this->object->auto(true); // turn on autowiring
        $this->assertTrue($this->object->has('BB'));
    }

    /**
     * shared object D
     *
     * @covers Phossa\Di\Container::get
     */
    public function testGet1()
    {
        // default autowiring is ON
        $a = $this->object->get('AA');
        $this->assertTrue($a->getB()->getD() === $a->getC()->getD());
    }

    /**
     * single object D
     *
     * @covers Phossa\Di\Container::get
     */
    public function testGet2()
    {
        // set single scope for class DD
        $this->object->add('DD', 'DD')->setScope(Container::SCOPE_SINGLE);

        $a = $this->object->get('AA');
        $this->assertFalse($a->getB()->getD() === $a->getC()->getD());
    }

    /**
     * Circular detection
     *
     * @covers Phossa\Di\Container::get
     * @expectedException Phossa\Di\Exception\LogicException
     * @expectedExceptionCode Phossa\Di\Message\Message::SERVICE_CIRCULAR
     */
    public function testGet3()
    {
        $this->object->get('ZAA');
    }

    /**
     * Object DD is shared only under QQ tree, different QQ has different DD
     *
     * @covers Phossa\Di\Container::get
     */
    public function testGet4()
    {
        include_once __DIR__ . '/testData4.php';

        // set container  default scope to single!!
        $this->object->share(false);

        // mark DD scope follows QQ
        $this->object->add('DD')->setScope('@QQ@');

        // DD is shared under same QQ
        $q1 = $this->object->get('QQ');
        $this->assertTrue($q1->getD() === $q1->getR()->getD());

        // try different QQ
        $q2 = $this->object->get('QQ');

        $this->assertTrue($q2->getD() === $q2->getR()->getD());
        $this->assertTrue($q1->getD() !== $q2->getD());
    }

    /**
     * Readme example 1
     *
     * @covers Phossa\Di\Container::get
     */
    public function testGet5()
    {
        $container = new Container();

        // use the 'MyCache' classname as the service id
        if ($container->has('MyCache')) {
            $cache = $container->get('MyCache');
            $this->assertTrue($cache instanceof \MyCache);
        } else {
            $this->assertFalse(true);
        }
    }


    /**
     * Test service alias
     *
     * @covers Phossa\Di\Container::get
     */
    public function testGet50()
    {
        $container = new Container();
        // alias
        $container->add('wow', '@MyCache@');

        $container->get('wow');
    }

    /**
     * Readme example 2, test definitions
     *
     * @covers Phossa\Di\Container::get
     */
    public function testGet6()
    {
        // turn off autowiring
        $container = (new Container())->auto(false);

        // config the cache service with classname and constructor arguments
        $container->add('cache', 'MyCache', [ '@cacheDriver@' ]);

        // add initialization methods after instantiation
        $container->add('cacheDriver', 'MyCacheDriver')
                  ->addMethod('setRoot', [ '%cache.root%' ]);

        // set a parameter which was used in 'cacheDriver'
        $container->set('cache.root', '/var/local/tmp');

        // get cache service by its id
        $cache = $container->get('cache');

        $this->assertTrue('/var/local/tmp' === $cache->getDriver()->getRoot());
    }

    /**
     * Readme example 3, add a callable
     *
     * @covers Phossa\Di\Container::get
     */
    public function testGet7()
    {
        // turn off autowiring
        $container = (new Container())->auto(false);

        // config the cache service with classname and constructor arguments
        $container->add('cache', 'MyCache', [ '@cacheDriver@' ]);

        // add initialization methods after instantiation
        $container->add('cacheDriver', function() {
                return new \MyCacheDriver();
                })->addMethod('setRoot', [ '%cache.root%' ]);

        // set a parameter which was used in 'cacheDriver'
        $container->set('cache.root', '/var/local/tmp');

        // get cache service by its id
        $cache = $container->get('cache');

        $this->assertTrue('/var/local/tmp' === $cache->getDriver()->getRoot());
    }

    /**
     * Readme example 4.1, load from two files
     *
     * @covers Phossa\Di\Container::get
     */
    public function testGet8()
    {
        // turn off autowiring
        $container = (new Container())->auto(false);
        $this->object = $container;

        // load service definitions
        $container->load(__DIR__ . '/definition.serv.php');

        // load parameter definition
        $container->load(__DIR__ . '/definition.param.php');

        // getting what you've already defined
        $cache = $container->get('cache');

        $this->assertTrue('/var/local/tmp' === $cache->getDriver()->getRoot());
    }

    /**
     * Readme example 4.2, load from one file
     *
     * @covers Phossa\Di\Container::get
     */
    public function testGet9()
    {
        // turn off autowiring
        $container = (new Container())->auto(false);
        $this->object = $container;

        // load definitions
        $container->load(__DIR__ . '/definition.php');

        // getting what you've already defined
        $cache = $container->get('cache');

        $this->assertTrue('/var/local/tmp' === $cache->getDriver()->getRoot());
    }

    /**
     * Readme example 5.1, test map to classname
     *
     * @covers Phossa\Di\Container::get
     */
    public function testGet10()
    {
        // turn off autowiring
        $container = new Container();

        // map a interface to a classname
        $container->map(
            'Phossa\\Cache\\CachePoolInterface',
            'Phossa\\Cache\\CachePool'
        );

        $container->get('\\Phossa\\Cache\\TestMap');
    }

    /**
     * Readme example 5.2, test map to service id
     *
     * @covers Phossa\Di\Container::get
     */
    public function testGet11()
    {
        // turn off autowiring
        $container = new Container();

        $container->add('cache', 'Phossa\\Cache\\CachePool');

        // map a interface to a classname
        $container->map(
            'Phossa\\Cache\\CachePoolInterface',
            '@cache@'
        );

        $container->get('\\Phossa\\Cache\\TestMap');
    }

    /**
     * Readme example 5.3, test map to parameter
     *
     * @covers Phossa\Di\Container::get
     */
    public function testGet12()
    {
        // turn off autowiring
        $container = new Container();

        $container->set('cache.class', 'Phossa\\Cache\\CachePool');

        // map a interface to a classname
        $container->map(
            'Phossa\\Cache\\CachePoolInterface',
            '%cache.class%'
        );

        $container->get('\\Phossa\\Cache\\TestMap');
    }

    /**
     * Readme example 5.4, load map from file
     *
     * @covers Phossa\Di\Container::get
     */
    public function testGet13()
    {
        // turn off autowiring
        $container = new Container();

        $container->set('cache.class', 'Phossa\\Cache\\CachePool');

        // map a interface to a classname
        $container->load(__DIR__.'/definition.map.php');

        $container->get('\\Phossa\\Cache\\TestMap');
    }

    /**
     * @covers Phossa\Di\Container::one
     */
    public function testOne()
    {
        $first  = $this->object->one('AA');
        $second = $this->object->one('AA');

        $this->assertTrue($first !== $second);

        // but dependencies is the same
        $this->assertTrue($first->getB() === $second->getB());
    }

    /**
     * @covers Phossa\Di\Container::run
     */
    public function testRun1()
    {
        $aa = $this->object->get('AA');
        $this->object->run(['@AA@', 'setX']);

        $this->assertTrue($aa->getX() instanceof \bingoXX);
    }

    /**
     * @covers Phossa\Di\Container::addTag
     */
    public function testAddTag()
    {
        $tags = ['WOW'];
        $ext = $this->invokeMethod(
            'getExtension',
            [ TaggableExtension::EXTENSION_CLASS ]
        );

        $this->assertFalse($ext->matchTags($tags));
        $this->object->addTag($tags);
        $this->assertTrue($ext->matchTags($tags));
    }

    /**
     * @covers Phossa\Di\Container::hasTag
     */
    public function testHasTag()
    {
        $tag = 'WOW';
        $this->assertFalse($this->object->hasTag($tag));
        $this->object->addTag($tag);
        $this->assertTrue($this->object->hasTag($tag));

        // emptyt tag return false
        $this->assertFalse($this->object->hasTag(''));
        $this->assertFalse($this->object->hasTag([]));
    }

    /**
     * normal test
     * @covers Phossa\Di\Container::setDelegate
     */
    public function testSetDelegate1()
    {
        $delegator = new \Phossa\Di\Extension\Delegate\Delegator();
        $this->object->setDelegate($delegator);
        $this->assertTrue($this->object === $delegator->getContainers()[0]);
    }

    /**
     * test auto() off for the last container
     *
     * @covers Phossa\Di\Container::setDelegate
     */
    public function testSetDelegate2()
    {
        $delegator = new \Phossa\Di\Extension\Delegate\Delegator();
        $container1 = new Container();
        $container2 = new Container();

        $container1->setDelegate($delegator);
        $container2->setDelegate($delegator, true);

        // autowiring is on
        $delegator->get('DD');

        // DD is in $container2
        $this->assertFalse($container1->has('DD'));
        $this->assertTrue($container2->has('DD'));
    }

    /**
     * normal addDecorate
     *
     * @covers Phossa\Di\Container::addDecorate
     */
    public function testSetDecorate1()
    {
        $aa1 = $this->object->get('AA');
        $this->assertNull($aa1->getD());

        // set container with decorating rules
        $this->object->addDecorate('setD', 'AA', ['setD', ['@DD@']]);

        // get new decorated aa
        $aa2 = $this->object->one('AA');
        $this->assertTrue($aa2->getD() instanceof \DD);
    }

    /**
     * addDecorate using tester callable etc.
     *
     * @covers Phossa\Di\Container::addDecorate
     */
    public function testSetDecorate2()
    {
        $aa1 = $this->object->get('AA');
        $this->assertNull($aa1->getD());

        // set container with decorating rules
        $container = $this->object;
        $this->object->addDecorate('setD', function($obj) {
            return $obj instanceof \AA;
        }, function ($obj) use ($container) {
            $obj->setD($container->get('DD'));
        });

        // get new decorated aa
        $aa2 = $this->object->one('AA');
        $this->assertTrue($aa2->getD() instanceof \DD);
    }

    /**
     * add provider instance
     *
     * @covers Phossa\Di\Container::addProvider
     */
    public function testAddProvider1()
    {
        // load prividers
        include_once __DIR__ . '/Extension/Provider/TestProvider.php';

        // not XX found
        $this->assertFalse($this->object->has('XX'));

        // provider
        $p = new \Phossa\Di\Extension\Provider\TestProvider();
        $this->object->addProvider($p);

        // now found XX
        $this->assertTrue($this->object->has('XX'));
        $this->assertTrue($this->object->get('XX') instanceof \bingoXX);
    }

    /**
     * add provider instance
     *
     * @covers Phossa\Di\Container::addProvider
     */
    public function testAddProvider2()
    {
        // load prividers
        include_once __DIR__ . '/Extension/Provider/TestTagProvider.php';

        // not 'bingo' found
        $this->assertFalse($this->object->has('bingo'));

        $this->object->addTag('TEST');

        // provider with tag 'TEST'
        $this->object->addProvider('Phossa\\Di\\Extension\\Provider\\TestTagProvider');

        // now found 'bingo'
        $this->assertTrue($this->object->has('bingo'));
        $this->assertTrue($this->object->get('bingo') instanceof \bingoXX);
    }

    /**
     * @covers Phossa\Di\Container::addExtension
     */
    public function testAddExtension()
    {
        $ext  = new \Phossa\Di\Extension\Provider\ProviderExtension;
        $name = $ext->getName();
        $this->object->addExtension($ext);
        $exts = $this->getPrivateProperty('extensions');
        $this->assertTrue($ext === $exts[$name]);
    }

    /**
     * @covers Phossa\Di\Container::set
     */
    public function testSet()
    {
        // right
        $this->object->set('dot.dot', 'CC');
        $params = $this->getPrivateProperty('parameters');
        $this->assertTrue('CC' === $params['dot']['dot']);
    }

    /**
     * @covers Phossa\Di\Container::add
     */
    public function testAdd()
    {
        $this->object->add('AA', 'CC');
        $this->assertTrue($this->object->get('AA') instanceof \CC);
    }

    /**
     * @covers Phossa\Di\Container::map
     */
    public function testMap1()
    {
        // get aa
        $aa = $this->object->get('AA');

        // map interface to classname
        $this->object->map('JJInterface', 'JJ');
        $kk = $this->object->get('KK');

        // map interface to $id
        $this->object->map('JJInterface', '@AA@');
        $kk = $this->object->one('KK');
        $this->assertTrue($aa === $kk->getJ());
    }

    /**
     * @covers Phossa\Di\Container::addMethod
     * @todo   Implement testAddMethod().
     */
    public function testAddMethod()
    {
        // test addMethod
        $this->object->add('AA')
                     ->addMethod('setMore', ['xx', 'yy']);
        $this->expectOutputString('CC xx DD yy CC yy DD xx ');
        $this->object->one('AA');

        $this->object->add('AA')
                     ->addMethod('setMore', ['@CC@', 'yy', 'xx']);
        $this->object->one('AA');
    }

    /**
     * @covers Phossa\Di\Container::setScope
     */
    public function testSetScope()
    {
        $this->object->add('AA')
             ->setScope('@WOW@');

        $aa1 = $this->object->get('AA');
        $aa2 = $this->object->get('AA', [], 'AntherScope');
        $this->assertFalse($aa1 === $aa2);

        $aa3 = $this->object->get('AA', [], '@WOW@');
        $this->assertTrue($aa1 === $aa3);
    }
}
