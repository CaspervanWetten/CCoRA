<?php

namespace Cora\Domain\Systems\Graphs;

use \Ds\Set as Set;
use \Ds\Map as Map;

class Graph implements \JsonSerializable
{
    protected $vertexes;
    protected $edges;
    protected $initial;

    public function __construct($vertexes = NULL, $edges = NULL, $initial = NULL)
    {
        $this->vertexes = new Map();
        $this->edges    = new Map();
        $this->initial  = NULL;

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

        if(!is_null($initial)) {
            $this->initial = $initial;
        }
    }

   /**
    * Add a vertex (node) to the graph
    * @param int $id The identifier for the vertex
    * @param mixed $vertex The vertex to add
    * @return void
    **/
    public function addVertex($id, $vertex)
    {
        $this->vertexes->put($id, $vertex);
    }

   /**
    * Add an edge to the graph
    * @param int $id The identifier for the edge
    * @param Edge $edge The edge to add
    * @return void
    **/
    public function addEdge($id, $edge)
    {
        $this->edges->put($id, $edge);
    }

   /**
    * Get all the edges pointing to the given vertex
    * @param int $id The identifier for the vertex
    * @return Map Map: EdgeId -> Edge
    **/
    public function preset($id)
    {
        $res = new Map();
        foreach($this->edges as $edgeId => $edge) {
            if($edge->to == $id) {
                $res->put($edgeId, $edge);
            }
        }
        return $res;
    }
    
   /**
    * Get all the edges pointing from the given vertex
    * @param int $id The identifier for the vertex
    * @return Map Map: EdgeId -> Edge
    **/
    public function postset($id)
    {
        $res = new Map();
        foreach($this->edges as $edgeId => $edge) {
            if($edge->from == $id) {
                $res->put($edgeId, $edge);
            }
        }
        return $res;
    }

   /**
    * Format the data for JSON representation. Used for
    * json_encode function
    * @return array Array of data to covert to json
    **/
    public function jsonSerialize() {
        $result = [
            "vertexes" => $this->vertexes->toArray(),
            "edges"    => $this->edges->toArray(),
            "initial"  => $this->initial
        ];
        return $result;
    }

   /**
    * Get the initial vertex (by id) of the graph
    **/
    public function getInitial()
    {
        if(!is_null($this->initial)) {
            return $this->initial;
        }
        return NULL;
    }

   /**
    * Get the value of belonging to a vertex id
    **/
    public function getKey($id)
    {
        if(!is_null($id) && $this->vertexes->hasKey($id)) {
            return $this->vertexes->get($id);
        }
        return NULL;
    }

   /**
    * Get the vertex map
    **/
    public function getVertexes()
    {
        return $this->vertexes;
    }
}

?>
