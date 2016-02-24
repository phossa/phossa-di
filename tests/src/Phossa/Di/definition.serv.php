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
