<?php

# system wide composer autoloader
$basedir = dirname(__DIR__);

if (file_exists(dirname($basedir).'/autoload.php')) {
    # netbeans-env autoloader
    $loader = require dirname($basedir).'/autoload.php';
} elseif (file_exists($basedir.'/vendor/autoload.php')) {
    # package autoloader
    $loader = require $basedir.'/vendor/autoload.php';
}

$loader->set('', dirname(__DIR__) . '/src/');
