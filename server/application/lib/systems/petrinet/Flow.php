<?php

namespace Cozp\Systems\Petrinet;

class Flow
{
    public $from;
    public $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }
}
 ?>
