# phossa-di

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

- Installation

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

- Simple usage

  You might have serveral simple classes like these or third party libraries, and
  want to make avaiable as services.

  ```php
  class Cache
  {
      private $driver;

      public function __construct(CacheDriver $driver)
      {
          $this->driver = $driver;
      }

      // ...
  }
  ```

  ```php
  class CacheDriver
  {
      // ...
  }
  ```

  You may just do the following,

  ```php
  use Phossa\Di\Container;

  $container = new Container();
  $cache = $container->get('Cache');
  ```

  With [auto wiring]((#auto)) is turnen on by default, the container will look
  for the `Cache` class and resolves its dependency automatically when create
  the cache object.

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
