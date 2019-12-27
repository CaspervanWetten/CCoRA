<?php

namespace Cora\Converters;

use \Cora\Systems\Petrinet\Petrinet as Petrinet;

class PetrinetToDot extends Converter
{
    protected $petrinet;  // the petrinet to convert
    
    public function __construct($petrinet, $translate = false)
    {
        $this->petrinet = $petrinet;
    }

    public function convert()
    {
        $placeStrings = $this->placesToDotStringArray();
        $transitionStrings = $this->transitionsToDotStringArray();
        $flowStrings = $this->flowsToDotStringArray();
        $markingStrings = $this->markingToDotStringArray();

        $font = "monospace";
        $fontsizeNode = "20";
        $fontsizeEdge = "14";

        $places = $this->petrinet->getPlaces();
        $transitions = $this->petrinet->getTransitions();
        $k = count($places) + count($transitions);
        $layout = "dot";

        $globalOptions = [
            sprintf("graph [fontname=\"%s\", fontsize=\"%s\"]", $font, $fontsizeNode),
            sprintf("node [fontname=\"%s\", fontsize=\"%s\"]", $font, $fontsizeNode),
            sprintf("edge [fontname=\"%s\", fontsize=\"%s\"]", $font, $fontsizeEdge),
            "ranksep=.5",
            "rankdir=TB",
            "nodesep=.5",
            "layout=".$layout,
            "outputorder=nodesfirst"
        ];

        $s = "digraph G {";
        $s .= "\n\t";
        $s .= implode("\n\t", $globalOptions);
        $s .= "\n\t";
        $s .= implode("\n\t", $placeStrings);
        $s .= "\n\t";
        $s .= implode("\n\t", $transitionStrings);
        $s .= "\n\t";
        $s .= implode("\n\t", $flowStrings);
        $s .= "\n\t";
        $s .= implode("\n\t", $markingStrings);
        $s .= "\n";
        $s .= "}";

        return $s;
    }

    protected function placesToDotStringArray()
    {
        $places = $this->petrinet->getPlaces();
        $names = [];
        foreach($places as $i => $place) {
            $s = $place;
            array_push($names, $s);
        }

        $options = sprintf(
            '[shape=%s, width=%s, height=%s, label=""]',
            "ellipse", .75, .75
        );

        $p = implode(", ", $names);
        $p = sprintf("%s %s;", $p, $options);

        $s = [$p];
        
        foreach($names as $i => $name) {
            $l = sprintf("%s [xlabel=\"%s\"]", $name, $name);
            array_push($s, $l);
        }
        return $s;
    }
    
    protected function transitionsToDotStringArray()
    {
        $fillColor = "#2ECC71";
        
        $transitions = $this->petrinet->getTransitions();
        $names = [];
        foreach($transitions as $i => $transition) {
            $t = $transition;
            array_push($names, $t);
        }
        
        $options = sprintf(
            '[shape="%s", style="%s", fillcolor="%s", width=%s, height=%s]',
            "box", "filled", $fillColor, 0.75, 0.75
        );

        $t = implode(", ", $names);
        $t = sprintf("%s %s;", $t, $options);
        
        return [$t];
    }

    protected function flowsToDotStringArray()
    {
        $flows = $this->petrinet->getFlows();
        $s = [];
        foreach($flows as $flow => $weight) {
            $from = $flow->from;
            $to   = $flow->to;
            $sub  = sprintf("%s -> %s", $from, $to);
            if($weight > 1) {
                $sub .= sprintf("[label=%d]", $weight);
            }
            $sub .= ";";
            array_push($s, $sub);
        }
        return $s;
    }

    protected function markingToDotStringArray()
    {
        $marking = $this->petrinet->getInitial();
        $s = [];
        foreach($marking as $place => $tokens)
        {
            if($tokens->value <= 0)
                continue;
            $l = sprintf("%s [label=\"%s\"];", $place, $tokens);
            array_push($s, $l);
        }
        return $s;
    }
}

?>
