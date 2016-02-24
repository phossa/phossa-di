<?php
namespace Phossa\Cache;

interface CachePoolInterface
{

}

class CachePool implements CachePoolInterface
{

}

class TestMap {
    private $cache;
    public function __construct(CachePoolInterface $cache) {
        $this->cache = $cache;
    }
    public function getCache() {
        return $this->cache;
    }
}
