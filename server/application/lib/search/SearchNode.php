<?php

namespace Cora\Search;

class SearchNode
{
    public $vertex;         // id
    public $predecessor;    // id

    public function __construct($vertex, $predecessor = NULL)
    {
        $this->vertex       = $vertex;
        $this->predecessor  = $predecessor;
    }
}

?>
