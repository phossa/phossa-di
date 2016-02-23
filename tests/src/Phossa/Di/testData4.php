<?php

class QQ {
    protected $r;
    protected $d;
    public function __construct(RR $r, DD $d) {
        $this->r = $r;
        $this->d = $d;
    }
    public function getR() {
        return $this->r;
    }
    public function getD() {
        return $this->d;
    }
}

class RR {
    protected $d;
    public function __construct(DD $d) {
        $this->d = $d;
    }
    public function getD() {
        return $this->d;
    }
}