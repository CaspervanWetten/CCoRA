<?php

namespace Cora\Converters;

use \Cora\Systems as Systems;
use \Cora\Search as Search;
use \Ds\Set as Set;

class JsonToGraph extends Converter
{
    protected $json;
    protected $petrinet;

    public function __construct($j, $p)
    {
        $this->json = $j;
        $this->petrinet = $p;
    }

    public function convert()
    {
        $json = $this->json;
        $petrinet = $this->petrinet;

        $states = $json["states"];
        $edges  = $json["edges"];
        $initial = NULL;
        if(isset($json["initial"]) && !is_null($json["initial"]["id"])) {
            $initial = intval($json["initial"]["id"]);
        }

        $vertexes = $this->convertMarkings($petrinet, $states);

        $newEdges = [];
        foreach($edges as $i => $edge) {
            $id = intval($edge["id"]);
            $from = intval($edge["fromId"]);
            $to = intval($edge["toId"]);
            $transition = $edge["transition"];
            if(isset($vertexes[$from]) && isset($vertexes[$to])) {
                $e = new Systems\Edge($from, $to, $transition);
                $newEdges[$id] = $e;
            }
        }
        $graph = new Systems\Graph($vertexes, $newEdges, $initial);
        if(isset($initial)) {
            foreach($vertexes as $id => $marking) {
                $bfs = new Search\BreadthFirstSearch($graph, $initial, $id);
                $marking->reachable = $bfs->pathExists();
            }
        }
        return $graph;
    }

    protected function convertMarkings($petrinet, $markings)
    {
        $vertexes =[];
        foreach($markings as $i => $marking) {
            $s = Systems\Marking::stringToMarking($petrinet, $marking["state"]);
            $vertexes[$markings[$i]["id"]] = $s;
        }
        return $vertexes;
    }
}

?>
