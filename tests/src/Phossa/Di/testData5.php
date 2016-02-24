<?php

class MyCache
{
    private $driver;

    public function __construct(MyCacheDriver $driver)
    {
          $this->driver = $driver;
    }

    public function getDriver()
    {
        return $this->driver;
    }
}

class MyCacheDriver
{
    private $root;

    public function setRoot($root)
    {
        $this->root = $root;
    }

    public function getRoot()
    {
        return $this->root;
    }
}
