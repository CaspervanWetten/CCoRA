<?php

namespace Cora\Domain\Graph;

use Cora\Domain\Graph\EdgeInterface as Edge;
use Cora\Domain\Graph\GraphInterface as IGraph;
use Cora\Domain\Petrinet\PetrinetInterface as IPetrinet;
use Cora\Domain\Petrinet\Transition\Transition;

use Cora\Utils\SetUtils;
use Exception;

class GraphBuilder implements GraphBuilderInterface {
    protected $vertexes;
    protected $edges;
    protected $initial;
    protected $petrinet;

    public function __construct(IPetrinet $petrinet) {
        $this->vertexes = new VertexMap();
        $this->edges = new EdgeMap();
        $this->petrinet = $petrinet;
    }

    public function addVertex(int $id, $vertex): void {
        $this->vertexes->addVertex($id, $vertex);
    }

    public function addEdge(int $id, Edge $edge): void {
        $this->edges->addEdge($id, $edge);
    }

    public function setInitial(?int $id): void {
        $this->initial = $id;
    }

    public function getGraph(): IGraph {
        if (!is_null($this->initial) &&
            !$this->vertexes->hasVertex($this->initial))
            throw new Exception("Could not create graph: initial id assigned " .
                                "to non-vertex element");
        $vertexIds = $this->vertexes->getIds();
        $edgeIds = $this->edges->getIds();
        if (!SetUtils::areDisjoint($vertexIds, $edgeIds))
            throw new Exception("Could not create graph: conflicting ids");
        $transitions = $this->petrinet->getTransitions();
        foreach($this->edges as $edge) {
            $label = $edge->getLabel();
            $trans = new Transition($label);
            if (!$transitions->contains($trans))
                throw new Exception("Could not create graph: graph labels do " .
                                    "not correspond with Petrinet transitions");
        }
        return new Graph($this->vertexes, $this->edges, $this->initial);
    }
}
