<?php
use Phossa\Di\Container;

  /* file name '*.php' indicating definitions in PHP format */
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
          'Phossa\\Cache\\CachePoolInterface'  => '\\Phossa\\Cache\\CachePool',
          // ...
      ],
  ];
