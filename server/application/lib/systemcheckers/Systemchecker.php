<?php

namespace Cora\SystemCheckers;

abstract class SystemChecker
{
    public $system;
    public function __construct($system)
    {
        $this->system = $system;
    }

    public abstract function check();
}

?>
