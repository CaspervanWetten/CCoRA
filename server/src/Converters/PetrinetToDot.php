<?php

namespace Cora\Converters;

use Cora\Domain\Petrinet\PetrinetInterface as Petrinet;

class PetrinetToDot extends Converter {
    protected $petrinet;

    public function __construct(Petrinet $net) {
        $this->petrinet = $net;
    }

    public function convert() {
        $placeStrings = $this->placesToArray();
        $transStrings = $this->transitionsToArray();
        $flowStrings  = $this->flowsToArray();

        $font = "monospace";
        $fontSizeNode = "20";
        $fontSizeEdge = "14";
        $options = [
            sprintf('graph [fontname="%s", fontsize="%s"]', $font, $fontSizeNode),
            sprintf('node [fontname="%s", fontsize="%s"]', $font, $fontSizeNode),
            sprintf('edge [fontname="%s", fontsize="%s"]', $font, $fontSizeEdge),
            "ranksep=.5",
            "rankdir=TB",
            "nodesep=.5",
            "outputorder=nodesfirst"
        ];
        $s = "digraph G {";
        $s .= "\n\t";
        $s .= implode("\n\t", $options);
        $s .= "\n\t";
        $s .= implode("\n\t", $placeStrings);
        $s .= "\n\t";
        $s .= implode("\n\t", $transStrings);
        $s .= "\n\t";
        $s .= implode("\n\t", $flowStrings);
        $s .= "\n";
        $s .= "}";
        return $s;
    }

    protected function placesToArray() {
        $places = $this->petrinet->getPlaces();
        $names = [];
        foreach($places as $place)
            array_push($names, $place->getName());
        $options = sprintf(
            '[shape=%s, width=%s, height=%s, label=""]',
            'ellipse', .75, .75
        );
        $p = implode(", ", $names);
        $p = sprintf("%s %s;", $p, $options);
        $res = [$p];
        foreach($names as $name)
            array_push($res, sprintf('%s [xlabel="%s"]', $name, $name));
        return $res;
    }

    protected function transitionsToArray() {
        $transitions = $this->petrinet->getTransitions();
        $names = [];
        foreach($transitions as $transition)
            array_push($names, $transition->getName());
        $options = sprintf(
            '[shape="%s", style="%s", fillcolor="%s", width=%s, height=%s]',
            'box', 'filled', '#2ECC71', .75, .75
        );
        $t = implode(", ", $names);
        $t = sprintf("%s %s;", $t, $options);
        return [$t];
    }

    protected function flowsToArray() {
        $flows = $this->petrinet->getFlows();
        $res = [];
        foreach($flows as $flow => $weight) {
            $row = sprintf("%s -> %s", $flow->getFrom(), $flow->getTo());
            if ($weight > 1)
                $row .= sprintf("[label=%d]", $weight);
            $row .= ";";
            array_push($res, $row);
        }
        return $res;
    }
}
