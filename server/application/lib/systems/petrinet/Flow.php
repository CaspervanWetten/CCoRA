<?php

namespace Cozp\Systems\Petrinet;

class Flow
{
    public $from;
    public $to;
    public $weight;

    public function __construct($from, $to, $weight=1)
    {
        $this->from = $from;
        $this->to = $to;
        $this->weight = $weight;
    }
}
 ?>
