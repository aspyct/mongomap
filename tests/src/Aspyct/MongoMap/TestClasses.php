<?php

namespace Aspyct\MongoMap;

class ObjectInflatorTestATestClass
{

    public $publicWSet;
    public $publicWoSet;
    protected $protectedWSet;
    protected $protectedWoSet;
    private $privateWSet;
    private $privateWoSet;

    public function getPublicWSet()
    {
        return $this->publicWSet;
    }

    public function setPublicWSet($publicWSet)
    {
        $this->publicWSet = $publicWSet;
    }

    public function getProtectedWSet()
    {
        return $this->protectedWSet;
    }

    public function setProtectedWSet($protectedWSet)
    {
        $this->protectedWSet = $protectedWSet;
    }

    public function getPrivateWSet()
    {
        return $this->privateWSet;
    }

    public function setPrivateWSet($privateWSet)
    {
        $this->privateWSet = $privateWSet;
    }

    public function defineProtectedWoSet($protectedWoSet)
    {
        $this->protectedWoSet = $protectedWoSet;
    }

    public function definePrivateWoSet($privateWoSet)
    {
        $this->privateWoSet = $privateWoSet;
    }

}

class ObjectInflatorTestBTestClass
{

    private $a;
    private $b;

    public function getA()
    {
        return $this->a;
    }

    public function setA($a)
    {
        $this->a = $a;
    }

    public function getB()
    {
        return $this->b;
    }

    public function setB($b)
    {
        $this->b = $b;
    }

}