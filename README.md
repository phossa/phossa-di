# phossa-di
[![Build Status](https://travis-ci.org/phossa/phossa-di.svg?branch=master)](https://travis-ci.org/phossa/phossa-di.svg?branch=master)
[![HHVM Status](http://hhvm.h4cc.de/badge/phossa/phossa-di.svg)](http://hhvm.h4cc.de/package/phossa/phossa-di)
[![Latest Stable Version](https://poser.pugx.org/phossa/phossa-di/v/stable)](https://packagist.org/packages/phossa/phossa-di)
[![License](https://poser.pugx.org/phossa/phossa-di/license)](https://packagist.org/packages/phossa/phossa-di)

Introduction
---

**Phossa-di** is a *fast*, *feature-rich* and *full-fledged* dependency
injection library forPHP. It supports [auto wiring](#auto),
[container delegation](#delegate), [object decorating](#decorate),
[definition provider](#provider), [definition tagging](#tag),
[object scope](#scope) and more.

It requires PHP 5.4 and supports PHP 7.0+, HHVM. It is compliant with
[PSR-1][PSR-1], [PSR-2][PSR-2], [PSR-4][PSR-4] and coming [PSR-5][PSR-5],
[PSR-11][PSR-11].

[PSR-1]: http://www.php-fig.org/psr/psr-1/ "PSR-1: Basic Coding Standard"
[PSR-2]: http://www.php-fig.org/psr/psr-2/ "PSR-2: Coding Style Guide"
[PSR-4]: http://www.php-fig.org/psr/psr-4/ "PSR-4: Autoloader"
[PSR-5]: https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md "PSR-5: PHPDoc"
[PSR-11]: https://github.com/container-interop/fig-standards/blob/master/proposed/container.md "Container interface"

Getting started
---

- **Installation**

  Install via the `composer` utility.

  ```
  composer require "phossa/phossa-di=1.*"
  ```

  or add the following lines to your `composer.json`

  ```json
  {
      "require": {
        "phossa/phossa-di": "^1.0.1"
      }
  }
  ```

- **Simple usage**

  You might have serveral simple classes like these or third party libraries,
  and want to make avaiable as services.

  ```php
  class MyCache
  {
      private $driver;

      public function __construct(MyCacheDriver $driver)
      {
          $this->driver = $driver;
      }

      // ...
  }
  ```

  ```php
  class MyCacheDriver
  {
      // ...
  }
  ```

  You may just do the following,

  ```php
  use Phossa\Di\Container;

  $container = new Container();

  // use the 'MyCache' classname as the service id
  if ($container->has('MyCache')) {
      $cache = $container->get('MyCache');
  }
  ```

  With [auto wiring]((#auto)) is turned on by default, the container will look
  for the `MyCache` class and resolves its dependency injection automatically
  when creating the `$cache` instance.

- **Use with definitions**

  Complex situations may need extra configurations. Definition related methods
  can be used to configure services.

  ```php
  use Phossa\Di\Container;

  // turn off auto wiring
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
  ```

  - *Service definitions*

    Service is defined using API `add($id, $classOrClosure, array $arguments)`
    and later can be refered in other definition with `@service_id@`

    ```php
    $container = new Container();

    // add the 'cache' service definition
    $container->add('cache', \Phossa\Cache\CachePool::class, ['@cacheDriver@']);

    // add the 'cacheDriver' service definition
    $container->add('cacheDriver', \Phossa\Cache\Driver\FilesystemDriver);

    // get cache service
    $cache = $container->get('cache');
    ```

    Service reference in the format of `@service_id@` can be used anywhere where
    an object is appropriate, such as in the argument array or construct a pseudo
    callable,

    ```php
    // will resolve this ['@cache@', 'setLogger'] to a real callable
    $container->run(['@cache@', 'setLogger'], ['@logger@']);
    ```

  - *Parameter definitions*

    Parameter can be set with API `set($name, $value)`. Parameter reference is
    '%parameter.name%'. Parameter reference can point to a string, another
    parameter or even a service reference.

    ```php
    // set system temp directory
    $container->set('system.tmpdir', '/var/tmp');

    // point cache dir to system temp
    $container->set('cache.dir', '%system.tmpdir%');

    // use parameter
    $container->add(
        'cacheDir',
        Phossa\Cache\Driver\Filesystem::class,
        [ '%cache.dir%' ]
    );
    ```

- **Callable instead of class name**

  Callable can be used to instantiate a service.

  ```php
  // ...
  $container->add('cacheDriver', function() {
      return new \MyCacheDriver();
  });
  ```

- **Definition files**

  Instead of configuring `$container` in the code, you may put your service and
  parameter definitions into one definition file or several seperated files
  *(seperating parameter definitions from service definitions will give you the
  benefit of loading different parameters base on different requirement etc.)*.

  PHP, JSON, XML file formats are supported, and will be detected automatically
  base on the filename suffixes.

  The service definition file `definition.serv.php`

  ```php
  <?php
  /* file name '*.s*.php' indicating SERVICE definitions in PHP format */
  use Phossa\Di\Container;
  return [
      'cache' => [
          'class' => [ 'MyCache', [ '@cacheDriver@' ]],
          'scope' => Container::SCOPE_SHARED // default anyway
      ],
      'cacheDriver' => [
          'class'   => 'MyCacheDriver',
          'methods' => [
              [ 'setRoot', [ '%cache.root%' ] ],
              // ...
          ]
      ],
      // ...
  ];

  ```

  The parameter definition file `definition.param.php`

  ```php
  <?php
  /* file name '*.p*.php' indicating PARAMETER definitions in PHP format */
  return [
      'cache.root' => '/var/local/tmp',
      // ...
  ];

  ```

  Or you may combine these files into one `definition.php`,

  ```php
  <?php
  /* file name '*.php' indicating definitions in PHP format */
  use Phossa\Di\Container;
  return [
      // key 'services' indicating the service definitions
      'services' => [
          'cache' => [
              'class' => [ 'MyCache', [ '@cacheDriver@' ]],
              'scope' => Container::SCOPE_SHARED // default anyway
          ],
          'cacheDriver' => [
              'class'   => 'MyCacheDriver',
              'methods' => [
                  [ 'setRoot', [ '%cache.root%' ] ],
                  // ...
              ]
          ],
          // ...
      ],

      // key 'parameters' indicating the parameter definitions
      'parameters' => [
          'cache.root' => '/var/local/tmp',
          // ...
      ],

      // key 'mappings' indicating the mapping definitions
      'mappings' => [
          'Phossa\\Cache\\CachePoolInterface'  => 'Phossa\\Cache\\CachePool',
          // ...
      ],
  ];

  ```

  You may load definitions from files now,

  ```php
  use Phossa\Di\Container;

  $container = new Container();

  // load service definitions
  $container->load('./definition.serv.php');

  // load parameter definition
  $container->load('./definition.param.php');

  // you may load from one if you want to
  // $container->load('./definition.php');

  // getting what you've already defined
  $cache = $container->get('cache');
  ```

Features
---

- <a name="auto"></a>**Auto wiring**

  *Auto wiring* is the ability of container instantiating objects and resolving
  its dependencies automatically. The base for auto wiring is the PHP function
  parameter *type-hinting*.

  By reflecting on the class, constructor and methods, *phossa-di* is able to
  find the right class for the object (user need to use the classname as the
  service id) and right class for the dependencies (type-hinted with the right
  classnames).

  To fully explore the auto wiring feature, users may map interface to classname
  or service id as the following,

  ```php
  // map an interface to a classname
  $container->map(
      'Phossa\\Cache\\CachePoolInterface', // MUST NO leading backslash
      'Phossa\\Cache\\CachePool' // leading backslash is optional
  );

  // map an interface to a service id, MUST NO leading backslash
  $container->map('Phossa\\Cache\\CachePoolInterface', '@cache@');

  // map an interface to a parameter, no leading backslash
  //$container->map('Phossa\\Cache\\CachePoolInterface', '%cache.class%');
  ```

  Or load mapping files,

  ```php
  $container->load('./defintion.map.php');
  ```

  Auto wiring can be turned on/off. Turn off auto wiring will enable user to
  check any defintion errors without automatically loading.

  ```php
  // turn off auto wiring
  $container->auto(false);

  // turn on auto wiring
  $container->auto(true);
  ```
- <a name="delegate"></a>**Container delegation**

  According to [Interop Container Delegate Lookup](https://github.com/container-interop/fig-standards/blob/master/proposed/container.md), container may register a delegate
  container (the delegator), and

  - Calls to the `get()` method should only return an entry if the entry is
    part of the container. If the entry is not part of the container, an
    exception should be thrown (as requested by the `ContainerInterface`).

  - Calls to the `has()` method should only return true if the entry is part
    of the container. If the entry is not part of the container, false should
    be returned.

  - If the fetched entry has dependencies, **instead** of performing the
    dependency lookup in the container, the lookup is performed on the
    delegate container (delegator).

  - **Important** By default, the lookup *SHOULD* be performed on the delegate
    container only, not on the container itself.

  phossa-di fully supports the delegate feature.

  ```php
  use Phossa\Di\Delegator;

  // create delegator
  $delegator = new Delegator();

  // insert different containers
  $delegator->addContainer($otherContainer);

  // $contaner register with the delegator
  $container->setDelegate($delegator);

  // cacheDriver is now looked up through the $delegator
  $cache = $container->get('cache');
  ```
- <a name="delegate"></a>**Object decorating**

  *Object decorating* is to apply decorating changes (call methods etc.) right
  after the instantiation of a service object base on certain criteria such as
  it implements an interface.

  ```php
  // any object implementing 'LoggerAwareInterface' should be decorated
  $container->addDecorate(
      'setlogger',  // rule name
      'Psr\\Log\\LoggerAwareInterface', // NO leading backslash
      ['setLogger', ['@logger@']] // run this method
  );
  ```

  *Object decorating* saves user a lot of definition duplications and will
  apply to future service definitions. Phossa-di also supports a tester
  callable and a decorate callable as follows,

  ```php
  $container->addDecorate('setlogger',
      function($object) {
          return $object instanceof \Psr\Log\LoggerAwareInterface;
      },
      function($object) use($container) {
          $object->setLogger($container->get('logger'));
      }
  );
  ```

- <a name="tag"></a>**Definition tagging**

  Most developers use different defintions or configurations for development
  or production environment. This is achieved by put definitions in different
  files and load these files base on the container tags.

  ```php
  // SYSTEM_CONST can be 'PRODUCTION' or 'DEVELOPMENT'
  $container->setTag([SYSTEM_CONST, OTHER_TAGS]);

  // load different defintion base on container tags
  if ($container->hasTag('PRODUCTION')) {
      $container->load('./productDefinitions.php');
  } else {
      $container->load('./developDefinitions.php');
  }
  ```

- <a name="provider"></a>**Definition provider**

  *Definition provider* is used to wrap logic related definitions into one
  entity. These definitions will be loaded into container automaitcally if a
  call to container's `has()` or `get()` and found the definition in this
  provider.

  ```php
  <?php

  use Phossa\Di\Extension\Provider\ProviderAbstract;

  // Production related DB definitions here
  class ProductionDbProvider extends ProviderAbstract
  {
      // list of service ids we provide
      protected $provides = [ 'DbServer' ];

      // tags
      protected $tags = [ 'PRODUCTION' ];

      // the only method we need to implement
      protected function mergeDefinition()
      {
          $container = $this->getContainer();
          $container->add('DbServer', '\\DbClass', [
              '192.168.0.12', 'myDbusername', 'thisIsApassword'
          ]);
      }
  }

  ```

  The previous provider `ProductionDbProvider` should be added into container
  before any calls to `has()` or `get()` and after setting the container tags
  (if tag feature used).

  ```php
  // SYSTEM_CONST is now 'PRODUCTION'
  $container->setTag(SYSTEM_CONST);

  // the provider will be loaded only if SYSTEM_CONST is PRODUCTION
  $container->addProvider(new ProductionDbProvider());

  // another provider will be loaded only if SYSTEM_CONST is TEST
  $container->addProvider(new TestDbProvider());

  // DB related definitions will be loaded here
  $db = $container->get('DbServer');
  ```

- <a name="scope"></a>**Object scope**

  By default, service objects in the container is shared inside the container,
  namely their has the scope of `Container::SCOPE_SHARED`. If users want
  different object each time, they may either use the method `one()` or define
  the service with `Container::SCOPE_SINGLE` scope.

  ```php
  // this will return the shared copy of cache service
  $cache1 = $container->get('cache');

  // this will always return a new copy of cache service
  $cache2 = $container->one('cache');

  // FALSE
  var_dump($cache1 === $cache2);

  // but both share the same cacheDriver since it is default to SCOPE_SHARED
  var_dump($cache1->getDriver() === $cache2->getDriver()); // true
  ```

  Or define it as `Container::SCOPE_SINGLE`

  ```php
  $container->add('cache', '\\Phossa\\Cache\\CachePool')
            ->setScope(Container::SCOPE_SINGLE);

  // each get() will return a new cache
  $cache = $container->get('cache');
  ```

  To make all service objects non-shared, set the container's default scope
  to `Container::SCOPE_SINGLE` as follows,

  ```php
  // make everything non-shareable, set default scope to SCOPE_SINGLE
  $container->share(false);

  // this will return a new copy of cache service
  $cache1 = $container->get('cache');

  // this will return a new copy also
  $cache2 = $container->get('cache');

  // FALSE
  var_dump($cache1 === $cache2);

  // to make everything shareable again, set default scope to SCOPE_SHARED
  $container->share(true);
  ```

Public APIs
--

- [PSR-11][PSR-11] compliant APIs

  - `get(string $id): object`

    Getting the named service from the container.

  - `has(string $id): bool`

    Check for the named service's existence in the container.

- Extended APIs by phossa-di

  - `__construct(string|array $definitions = '', array $providers = [])`

    `$defintions` can be a defintion filename or definition array.

    `$providers` can be array of `ProviderAbstract` objects or provider
    classnames.

  - `get(string $id, array $arguments = [], string $scope = ''): object`

    If extra arguments are provided, new instance will be generated even if
    it was configured with a `Container::SCOPE_SHARED` scope.

    *Arguments may contain references like `@service_id@` or `%parameter%`*.

  - `one(string $id, array $arguments = []): object`

    Get a new instance even if it is configured as a shared service with or
    without new arguments.

  - `run(callable|array $callable, array $arguments = []): mixed`

    Execute a callable with the provided arguments. Pseudo callable like
    `['@cacheDriver@', '%cache.setroot.method%']` is supported.

- Definition related APIs

  - `add(string|array $id, string|callable $className, array $arguments = []): this`

    Add a service definition or definitions(array) into the container. Callable
    can be used instead of classname to create an instance. `$arguments` is
    for the constructor.

    Aliasing can be achieved by define `$className` as a service reference,
    namely `@serviceId@`.

  - `set(string|array $name, string $value = ''): this`

    Set a parameter or parameters(array) into the container.

  - `map(string|array $interface, string $className): this`

    Map an interface name or a classname to a classname, a service id or a
    predefiend parameter. Map array can be inserted into container if
    `$interface` is an array.

    **Note** No leading backslash for the `$interface`

  - `share(bool $status = true): this`

    Set container-wide default scope. `true` to set to `Container::SCOPE_SHARED`
    and `false` set to `Container::SCOPE_SINGLE`

  - `addMethod(string $method, array $arguments = []): this`

    Add a method call to the previous added service in the chain of `add()` or
    `addMethod()`.

  - `setScope(string $scope): this`

    Set scope for the previous added service in the chain of `add()` or
    `addMethod()`. There are two predefined scope contants, shared scope
    `Container::SCOPE_SHARED` and single scope `Container::SCOPE_SINGLE`.

  - `auto(bool $status): this`

    Turn on (true) or turn off (false) [auto wiring](#auto).

- Extension related APIs

  - `addExtension(ExtensionAbstract $extension): this`

    Explicitly load an extension into the container.

  - `load(string|array $fileOrArray): this`

    **LoaderExtension**  load a definition array or definition file into the
    container. Definition filename with the format of `*.s*.php` will be
    considered as a service definition file in PHP format. `*.p*.php` is a
    parameter file in PHP format. `*.m*.php` is a mapping file.

    File suffixes '.php|.json|.xml' are known to this library.

  - `setTag(string|array $tag): this`

    **TaggableExtension**  set container tags. Tags can be used to selectly
    load definition files or definition providers.

  - `hasTag(string|array $tag): bool`

    **TaggableExtension** check the existence of tags in the container. One tag
    match will return `true`, otherwise return `false`

    ```php
    if ($container->hasTag('PRODUCTION')) {
        $container->load('./productDefinitions.php');
    } else {
        $container->load('./developDefinitions.php');
    }
    ```

  - `setDelegate(DelegatorInterface $delegator): this`

    **DelegateExtension**  set the [delegator](https://github.com/container-interop/fig-standards/blob/master/proposed/container.md#13-additional-feature-delegate-lookup).
    Dependency will be looked up in the delegator instead of in the container.
    The container itself will be injected into delegator's container pool.

    Since [auto wiring](#auto) is conflict with the delegation design, auto
    wiring will be turned off automatically for containers in the pool except
    for the last one.

    ```php
    use Phossa\Di\Extension\Delegate\Delegator;

    // create the delegator
    $delegator = new Delegator();

    // other container register with the delegator
    $delegator->addContainer($otherContainer);

    /*
     * register $container with its auotwiring status unchanged (last container)
     * but $otherContainer's autowiring will be forced off
     */
    $container->setDelegate($delegator);

    // dependency will be resolved in the order of $otherContainer, $container
    // ...
    ```

  - `addDecorate(string $name, string|callable $tester, array|callable $decorator): this`

    **DecorateExtension** adding object decorating rules to the container.

  - `addProvider(string|ProviderInterface $provider): this`

    **ProviderExtension** add definition provider to the container either by
    provider classname or a provider object.

Version
---

1.0.5

Dependencies
---

- PHP >= 5.4.0

- phossa/phossa-shared >= 1.0.6

- container-interop/container-interop ~1.0

License
---

[MIT License](http://spdx.org/licenses/MIT)
