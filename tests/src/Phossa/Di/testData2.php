<?php

class XAA {
    protected $b;
    protected $c;
    public function __construct(XBB $b, XCC $c) {
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

class XBB {
    protected $d;
    public function __construct(XDD $d) {
        $this->d = $d;
    }
    public function getD() {
        return $this->d;
    }
}

class XCC {
    protected $d;
    public function __construct(XDD $d) {
        $this->d = $d;
    }
    public function getD() {
        return $this->d;
    }
}

class XDD {
    public function __construct() {
    }
}

// circular
class ZAA {
    protected $b;
    public function __construct(ZBB $b) {
        $this->b = $b;
    }
}
class ZBB {
    protected $a;
    public function __construct(ZAA $a, $x = '') {
        $this->a = $a;
    }
}