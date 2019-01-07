<?php

namespace Cora\Systems;

class Edge
{
    public $from;
    public $to;
    public $label;

    public function __construct($from, $to, $label = "") {
        $this->from  = $from;
        $this->to    = $to;
        $this->label = $label;
    }
}

?>
