<?php

namespace Cora\Systems;

use \Cora\Enumerators\GraphSetType as GraphSetType;
use \Ds\Set as Set;
use \Ds\Map as Map;

class Graph
{
    public $vertexes;
    public $edges;
    public $initial;

    public function __construct($vertexes = NULL, $edges = NULL, $initialMarking = NULL)
    {
        $this->vertexes = new Map();
        $this->edges = new Map();
        $this->initial = NULL;

        if(!is_null($vertexes)) {
            foreach($vertexes as $id => $vertex) {
                $this->vertexes->put($id, $vertex);
            }
        }

        if(!is_null($edges)) {
            foreach($edges as $i => $edge) {
                $this->edges->put($i, $edge);
            }
        }

        if(!is_null($initialMarking)) {
            $this->initial = $initialMarking;
        }
    }

    public function addEdge($id, $edge)
    {
        $this->edges->put($id, $edge);
    }

    public function addMarking($id, $marking)
    {
        $this->markings->put($id, $marking);
    }

    public function getPostSet($id, $mode = GraphSetType::Vertex)
    {
        $edges = $this->edges;
        $res = new Map();
        foreach($edges as $edgeId => $edge)
        {
            if($edge->fromId == $id) {
                $element;
                switch($mode) {
                    case GraphSetType::Vertex:
                        $element = $edge->toId;
                        break;
                    case GraphSetType::Edge:
                        $element = $edge;
                        break;
                    default:
                        $element = $edge->toId;
                }
                $res->put($edgeId, $element);
            }
        }
        return $res;
    }

    public function getPreSet($id, $mode = GraphSetType::Vertex)
    {
        $edges = $this->edges;
        $res = new Map();
        foreach($edges as $edgeId => $edge)
        {
            if($edge->toId == $id) {
                $element;
                switch($mode) {
                    case GraphSetType::Vertex:
                        $element = $edge->fromId;
                        break;
                    case GraphSetType::Edge:
                        $element = $edge;
                        break;
                    default:
                        $element = $edge->fromId;
                }
		    $res->put($edgeId, $element);
            }
        }
        return $res;
    }
    
    public function getInitialVertex()
    {
        if(!is_null($this->initial)) {
            return $this->initial;
        }
        return NULL;
    }

    public function getVertex($id)
    {
        if($this->vertexes->hasKey($id)) {
            return $this->vertexes->get($id);
        }
        return NULL;
    }

    public function getVertexes()
    {
        return $this->vertexes;
    }
}

?>
