<?php

class AA implements JJInterface {
    protected $b;
    protected $c;
    protected $d;
    protected $x;
    public function __construct(BB $b, CC $c) {
        $this->b = $b;
        $this->c = $c;
    }
    public function getB() {
        return $this->b;
    }
    public function getC() {
        return $this->c;
    }
    public function setD(DD $d) {
        $this->d = $d;
    }
    public function getD() {
        return $this->d;
    }
    public function setX(bingoXX $x) {
        $this->x = $x;
    }
    public function getX() {
        return $this->x;
    }
    public function setMore(CC $c, $x, DD $d, $y) {
        echo get_class($c)." $x ".get_class($d)." $y ";
    }
}

class BB {
    protected $d;
    protected $e;
    public function __construct(DD $d) {
        $this->d = $d;
    }
    public function getD() {
        return $this->d;
    }
    public function setE(EE $e) {
        $this->e = $e;
    }
    public function getE() {
        return $this->e;
    }
}

class CC {
    protected $d;
    public function __construct(DD $d) {
        $this->d = $d;
    }
    public function getD() {
        return $this->d;
    }
}

class DD {
    public function __construct() {
    }
}

class EE {
}

// for provider testing
class bingoXX {
}

interface JJInterface {

}

class JJ implements JJInterface {

}

class KK {
    protected $j;
    public function __construct(JJInterface $j) {
        $this->j = $j;
    }
    public function getJ() {
        return $this->j;
    }
}