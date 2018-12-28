<?php

namespace Cozp\Converters;

use Cozp\Systems as Systems;
use Cozp\Search as Search;

class JsonToCoverabilityGraph extends JsonToGraph
{
    public function convert()
    {
        $graph = parent::convert();
        $vertexes = $graph->getVertexes();
        $initial = $graph->getInitialVertex();
        if(isset($initial)) {
            foreach($vertexes as $id => $vertex) {
                $bfs = new Search\BreadthFirstSearch($graph, $initial, $id);
                $path = $bfs->pathExists();
                $vertex->reachableFromInitial = $path;
            }
        }
        return $graph;
    }

    protected function convertMarkings($petrinet, $markings)
    {
        $vertexes = [];
        foreach($markings as $i => $marking) {
            $s = Systems\CoverabilityMarking::stringToMarking($petrinet, $marking["state"]);
            $vertexes[$markings[$i]["id"]] = $s;
        }
        return $vertexes;
    }
}

?>