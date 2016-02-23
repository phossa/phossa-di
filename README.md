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
[definition tags](#tag) and more.

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

- **Definition files**

  Instead of configuring $container in the code, you may put your service and
  parameter definitions into one definition file or two files (seperating
  parameter definitions from service definitions will give you the benefit
  of loading different parameters base on different requirement).

  PHP, Json, XML format of definitioin formats are supported.

  The service definition file `definition.serv.php`

  ```php
  <?php
  /* file name '*.s[.]*.php' indicating a service definition in PHP format */
  return [
      'cache' => [
          'class' => [ 'MyCache', [ '@cacheDriver@' ]]
      ],
      'cacheDriver' => [
          'class'   => [ 'MyCacheDriver' ],
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
              'class'   => [ 'MyCacheDriver' ],
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

  You can load definitions from file file now,

  ```php
  use Phossa\Di\Container;

  $container = new Container();

  // load service definitions
  $container->load('./definition.serv.php');

  // load parameter definition
  $container->load('./definition.param.php');

  // you may load one file if you want to
  // $container->load('./definitions.php');

  // getting what you've already defined
  $cache = $container->get('cache');
  ```

Features
---

- <a name="auto"></a>Auto wiring

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
