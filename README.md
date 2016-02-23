# phossa-di
[![Build Status](https://travis-ci.org/phossa/phossa-di.svg?branch=master)](https://travis-ci.org/phossa/phossa-di.svg?branch=master)
[![HHVM Status](http://hhvm.h4cc.de/badge/phossa/phossa-di.svg)](http://hhvm.h4cc.de/package/phossa/phossa-di)
[![Latest Stable Version](https://poser.pugx.org/phossa/phossa-di/v/stable)](https://packagist.org/packages/phossa/phossa-di)
[![License](https://poser.pugx.org/phossa/phossa-di/license)](https://packagist.org/packages/phossa/phossa-di)

Introduction
---

Phossa-di is a **FAST** and **FULL-FLEDGED** dependency injection library for
PHP. It supports [auto wiring](#auto), [container delegation](#delegate),
[object decorating](#decorate), [definition provider](#provider),
[definition tags](#tag), [service scope](#scope) and more.

It requires PHP 5.4 and supports PHP 7.0+, HHVM. It is compliant with
[PSR-1][PSR-1], [PSR-2][PSR-2], [PSR-4][PSR-4] and the coming [PSR-5][PSR-5],
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
  $cache = $container->get('MyCache');
  ```

  With [auto wiring]((#auto)) is turnen on by default, the container will look
  for the `MyCache` class and resolves its dependency injection automatically
  when creating the cache instance.

- **Use with definitions**

  Complex situations may need extra configurations. Definition related methods
  can be used to configure services.

  ```php
  use Phossa\Di\Container;

  $container = new Container();

  // config the cache with classname and constructor arguments
  $container->add('cache', 'MyCache', [ '@cacheDriver@' ]);

  // config the cache driver with extra init method
  $container->add('cacheDriver', 'MyCacheDriver')
            ->addMethod('setRoot', [ '%cache.root%' ]);

  // set a parameter which was used in 'cacheDriver'
  $container->set('cache.root', '/var/local/tmp');

  // get cache object by id 'cache'
  $cache = $container->get('cache');
  ```

  In the definition, another service can be referenced as '@cacheDriver@' and
  parameter can be referenced as '%cache.root%'.

- **Callable instead of class name**

  Callable can be used to instantiate a service.

  ```php
  // ...
  $container->add('cacheDriver', function() {
      return new \cacheDriver();
  });
  ```

- **Definition files**

  Instead of configuring $container in the code, you may put your service and
  parameter definitions into one definition file or two files *(seperating
  parameter definitions from service definitions will give you the benefit
  of loading different parameters base on different requirement)*.

  PHP, JSON, XML format of definitioin formats are supported, and will be
  automatically detected base on the filename suffix.

  The service definition file `definition.serv.php`

  ```php
  <?php
  /* file name '*.s[.]*.php' indicating a service definition in PHP format */
  return [
      'cache' => [
          'class' => [ 'MyCache', [ '@cacheDriver@' ]]
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
  /* file name '*.p[.]*.php' indicating a parameter definition in PHP format */
  return [
      'cache.root' => '/var/local/tmp',
      // ...
  ];

  ```

  Or you may combine these two files into one `definitions.php`,

  ```php
  <?php
  /* file name '*.php' indicating a complete definition in PHP format */
  return [
      // key 'services' indicating the service definition part
      'services' => [
          'cache' => [
              'class' => [ 'MyCache', [ '@cacheDriver@' ]]
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

      // key 'parameters' indicating the parameter definition part
      'parameters' => [
          'cache.root' => '/var/local/tmp',
          // ...
      ]
  ];

  ```

  You can load definitions from files now,

  ```php
  use Phossa\Di\Container;

  $container = new Container();

  // load service definitions
  $container->load('./definition.serv.php');

  // load parameter definition
  $container->load('./definition.param.php');

  // you may load from one if you want to
  // $container->load('./definitions.php');

  // getting what you've already defined
  $cache = $container->get('cache');
  ```

Features
---

- <a name="auto"></a>Auto wiring

Public APIs
--

- [PSR-11][PSR-11] compliant APIs

  - `get(string $id): object`

    Getting the named service from the container.

  - `has(string $id): bool`

    Check for service existence in the container.

- Extended APIs by phossa-di

  - `get(string $id, array $arguments = [], string $scope = ''): object`

    Provided with extra arguments to get a different instance even if it was
    configured as a shared service. Set a new scope with `$scope` instead of
    the configured scope.

    **Arguments may contain references like `@service_id@` or `%parameter%`**.

  - `one(string $id, array $arguments = []): object`

    Get a new instance even if it is configured as a shared service with or
    without different arguments from the configured ones.

  - `run($callable, array $arguments = []): mixed`

    Execute a callable with the provided arguments. The callable can also be
    a pseudo callable like `['@cacheDriver@', 'setRoot']`.

- Definition related APIs

  - `add(string|array $id, string|callable $class, array $arguments = []): this`

    Add a service definition or definitions(array $id) into the container.
    Callable can be used instead of classname to create an instance.

  - `set(string|array $name, string|callable $value = ''): this`

    Set a parameter or parameters(array $name) into the container. `$value`
    be a string or a callable (callable will be executed when this parameter
    is being used).

  - `map(string|array $interface, string $classname): this`

    Map a interface name to a classname or a service id.

  - `addMethod(string $method, array $arguments = []): this`

    Add a method call to the previous added service in the chain of `add()` or
    `addMethod()`.

  - `addScope(string $scope): this`

    Add a scope to the previous added service in the chain of `add()` or
    `addMethod()`. There are two predefined scope contants, shared scope
    `Container::SCOPE_SHARED` and single scope `Container::SCOPE_SINGLE`.

    **NOTE**: if you want to share a dependent instance only under a specific
    ancester service, you may define the as the ancester service id

    ```php
    $container->add('cache', 'MyCache');
    $container->add('cacheDriver', 'MyCacheDriver');

    $cache1 = $container->one('cache');
    $cache2 = $container->one('cache');

    // $cache1 !== $cache2, but cacheDriver is shared
    var_dump($cache1 === $cache2); // false
    var_dump($cache1->getDriver() === $cache2->getDriver()); // true

    // reconfigure cacheDriver scope, it coupled with 'cache' instance now
    $container->add('cacheDriver', 'MyCacheDriver')->addScope('cache');

    $cache3 = $container->one('cache');
    var_dump($cache1->getDriver() === $cache3->getDriver()); // false
    ```

  - `auto(bool $status): this`

    Turn on ($status = true) or turn off ($status = false) [auto wiring](#auto).

- Extension related APIs

  - `addExtension(ExtensionInterface $extension): this`

    Dynamically load an user defined (extends ExtensionAbstract) extension into
    the container.

  - `load(string|array $fileOrArray, array $tags = []): this`

    Load a definition array or definition file into the container. Definition
    filename with the format of '*.s*.php' will be considered as a service
    definition file in PHP format. '*.p*.php' is a parameter file in PHP format,
    '*.m*.php' is a mapping file. File suffixes '.php|.json|.xml' is known to
    this library.

    `$tags` is used when loading from a defintion file, the loader extension
    will compare container's tags with `$tags`, if matches found, then the
    definition file will be loaded. *IF `$tags` is empty, the definition file
    will ALWAYS be loaded.*

  - `setTags(array $tags): this`

    Set container tags. Tags can be used to selectly load definition files or
    definition providers.

    ```php
    if ($container->hasTags(['PRODUCTION'])) {
        $container->load('./productDefinitions.php');
    } else {
        $container->load('./developDefinitions.php');
    }
    ```

  - `setDelegate(DelegatorInterface $delegator, bool $keepAutowiring = false): this`

    Set the delegator. Dependency will be looked up in the delegator instead
    of in the container. The container itself will be injected into delegator's
    container pool.

    Since [auto wiring](#auto) is conflict with the delegation design, this
    feature will be turned off automatically. But user may choose to keep
    auto wiring on if the container is the last on in the delegator's container
    pool.

    ```php
    use Phossa\Di\Extension\Delegate\Delegator;

    // create the delegator
    $delegator = new Delegator();

    // other container may register with the delegator
    $delegator->setContainer($otherContainer);

    // register self with delegator
    $container->setDelegate($delegator);

    // dependency will be resolved in the order of $otherContainer, $container
    // ...
    ```

  - `setDecorate(string $name, string|callable $tester, array|callable $decorator): this`

    Set the decorating methods or callables for the matching service object, if
    `TRUE` returned from `$tester` (callable) or is an instance of the `$tester`
    (string).

  - `addProvider(string|ProviderInterface $provider): this`

    Add definition provider to the container either by provider classname or
    the provider object.

Version
---

1.0.1

Dependencies
---

- PHP >= 5.4.0

- phossa/phossa-shared >= 1.0.6

- container-interop/container-interop ~1.0

License
---

[MIT License](http://spdx.org/licenses/MIT)
