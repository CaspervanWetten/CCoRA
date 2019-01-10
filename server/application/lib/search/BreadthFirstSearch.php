<?php

namespace Cora\Search;

use \Cora\Systems;
use \Ds\Queue as Queue;
use \Ds\Set as Set;
use \Ds\Map as Map;

class BreadthFirstSearch
{
    public $graph;
    public $from;
    public $to;

    public function __construct($graph, $from, $to)
    {
        $this->graph = $graph;
        $this->from = $from;    // id
        $this->to = $to;        // id
    }

    public function getPath()
    {
        $graph = $this->graph;
        $from = $this->from;
        $to = $this->to;

        $frontier = new Map();      // grey. to parent
        $closed   = new Map();      // black. to parent 
        $q        = new Queue();

        $frontier->put($from, NULL);
        $q->push($from);
        while(!$q->isEmpty()) {
            $current = $q->pop();
            $parent = $frontier->get($current);

            if($current == $to) {
                $closed->put($current, $parent);
                return $this->backtracePath($current, $closed);
            }

            $post = $graph->postset($current);
            foreach($post as $id => $edge) {
                if(!$frontier->hasKey($edge->to) && !$closed->hasKey($edge->to)) {
                    $q->push($edge->to);
                    $frontier->put($edge->to, $current);
                }
            }
            $frontier->remove($current);
            $closed->put($current, $parent);
        }
        return NULL;
    }

    public function pathExists()
    {
        $path = $this->getPath();
        return !is_null($path);
    }

    protected function backtracePath($last, $closed)
    {
        $path = [];
        array_push($path, $last);
        $predecessor = $closed->get($last);
        
        while(!is_null($predecessor)) {
            array_push($path, $predecessor);
            $predecessor = $closed->get($predecessor);
        }
        return array_reverse($path);
    }
}

?>
