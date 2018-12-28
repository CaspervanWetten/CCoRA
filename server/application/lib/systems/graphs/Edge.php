<?php

namespace Cozp\Systems;

class Edge
{
    public $fromId;
    public $toId;
    public $label;

    public function __construct($from, $to, $label = "") {
        $this->fromId = $from;
        $this->toId = $to;
        $this->label = $label;
    }
}

?>