<?php

class YAA {
    protected $b;
    protected $c;
    public function __construct(YBB $b, XCC $c) {
        $this->b = $b;
        $this->c = $c;
    }
    public function getB() {
        return $this->b;
    }
    public function getC() {
        return $this->c;
    }
}

class YBB {
    protected $d;
    public function __construct(DD $d) {
        $this->d = $d;
    }
    public function getD() {
        return $this->d;
    }
}