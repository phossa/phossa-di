<?php

# system wide composer autoloader
$basedir = dirname(__DIR__);

if (file_exists(dirname($basedir).'/autoload.php')) {
    # systemwide autoloader
    $loader = require dirname($basedir).'/autoload.php';
} elseif (file_exists($basedir.'/vendor/autoload.php')) {
    # package autoloader
    $loader = require $basedir.'/vendor/autoload.php';
}

# add default namespace map
if (isset($loader)) {
    $loader->set('', dirname(__DIR__) . '/src/');
}
