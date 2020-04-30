<?php

namespace Cora\Converters;

use Cora\Domain\Graphs\Edge;
use Cora\Domain\Graphs\GraphBuilder;
use Cora\Domain\Graphs\GraphBuilderInterface as IGraphBuilder;

use Cora\Domain\Petrinet\PetrinetInterface as Petrinet;
use Cora\Domain\Petrinet\Place\Place;
use Cora\Domain\Petrinet\Marking\MarkingBuilder;
use Cora\Domain\Petrinet\Marking\Tokens\IntegerTokenCount;
use Cora\Domain\Petrinet\Marking\Tokens\OmegaTokenCount;

use Exception;

class JsonToGraph extends Converter {
    protected $json;
    protected $petrinet;

    public function __construct(string $json, Petrinet $petrinet) {
        $this->json = json_decode($json, true);
        $this->petrinet = $petrinet;
    }

    public function convert() {
        $json = $this->json;
        if (!array_key_exists("states", $json) ||
            !array_key_exists("edges", $json))
            throw new Exception("Could not parse graph: incorrect format");

        $builder = new GraphBuilder($this->petrinet);
        $this->addVertexes($builder);
        $this->addEdges($builder);
        $this->addInitial($builder);
        return $builder->getGraph($this->petrinet);
    }

    protected function addVertexes(IGraphBuilder &$builder) {
        $json = $this->json;
        $states = $json["states"];
        foreach($states as $stateA) {
            if (!array_key_exists("state", $stateA) ||
                !array_key_exists("id", $stateA))
                throw new Exception("Could not parse graph: incorrect format");
            $vertexId = intval($stateA["id"]);
            $marking = $this->stringToMarking($stateA["state"]);
            $builder->addVertex($vertexId, $marking);
        }
    }

    protected function addEdges(IGraphBuilder &$builder) {
        $json = $this->json;
        foreach($json["edges"] as $edgeA) {
            if (!array_key_exists("fromId", $edgeA) ||
                !array_key_exists("toId", $edgeA) ||
                !array_key_exists("transition", $edgeA) ||
                !array_key_exists("id", $edgeA))
                throw new Exception("Could not parse graph: invalid format");
            $from = intval($edgeA["fromId"]);
            $to = intval($edgeA["toId"]);
            $transition = trim($edgeA["transition"]);
            $id = intval($edgeA["id"]);
            $edge = new Edge($from, $to, $transition);
            $builder->addEdge($id, $edge);
        }
    }

    protected function addInitial(IGraphBuilder &$builder) {
        $json = $this->json;
        if (array_key_exists("initial", $json)) {
            if (!array_key_exists("id", $json["initial"]))
                throw new Exception("Could not parse graph: incorrect format");
            $id = intval($json["initial"]["id"]);
            $builder->setInitial($id);
        }
    }

    protected function stringToMarking(string $s) {
        $builder = new MarkingBuilder();
        $s = preg_replace('/ /', '', $s);
        $pairs = explode(",", $s);
        foreach($pairs as $pair) {
            list($p, $t) = preg_split("/:/", $pair, NULL, PREG_SPLIT_NO_EMPTY);
            if (is_numeric($t))
                $tc = new IntegerTokenCount(intval($t));
            else
                $tc = new OmegaTokenCount();
            $place = new Place($p);
            $builder->assign($place, $tc);
        }
        return $builder->getMarking($this->petrinet);
    }
}
