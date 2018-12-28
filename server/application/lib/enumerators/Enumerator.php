<?php

namespace Cozp\Enumerators;

use ReflectionClass;

abstract class Enumerator
{
    protected $reflection;
    public function __construct()
    {
    }
    public function getConstants()
    {
        if ($this->reflection == NULL)
            $this->reflection = new ReflectionClass($this);
        return $this->reflection->getConstants();
    }
}

 ?>
