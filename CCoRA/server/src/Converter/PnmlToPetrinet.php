<?php

namespace Cora\Converter;

use Cora\Utils\Printer;

use Cora\Domain\Petrinet\PetrinetBuilder;
use Cora\Domain\Petrinet\Place\Place;
use Cora\Domain\Petrinet\Transition\Transition;
use Cora\Domain\Petrinet\Flow\Flow;
use Cora\Domain\Petrinet\Marking\Tokens\IntegerTokenCount;
use Cora\Domain\Petrinet\Marking\MarkingBuilder;
use Cora\Domain\Petrinet\MarkedPetrinet;

use Exception;

class PnmlToPetrinet extends Converter {
    protected $pnml;
    protected $printer;

    public function __construct(string $pnml) {
        $this->pnml = $pnml;
        $this->printer = new Printer;
    }

    public function convert() {
        //Initialize the DOMElement, SimpleXMLElement (used for Xpath) and the Petrinet Builder
        $doc = new \DOMDocument();
        $doc->loadXML($this->pnml);
        $builder = new PetrinetBuilder();

        //Check if there are multiple pages
        $pages = $doc->getElementsByTagName('page');
        if ($pages->length > 1) {
            throw new Exception("CoRA2 Does not support pnml files with multiple petri nets at this time");
        }

        
        // Parse places
        $places = $doc->getElementsByTagName('place');
        foreach ($places as $place) {
            //get the id via attribute
            $id = $place->getAttribute('id');
            //get the coordinates via tag
            $positionX = $place->getElementsByTagName('position')->item(0)->getAttribute('x');
            $positionY = $place->getElementsByTagName('position')->item(0)->getAttribute('y');    
            $coordinates = array($positionX, $positionY);
            //get the text via xpath
            $text = $place->getElementsByTagName('text')[0]->nodeValue;
            //add the place
            $builder->addPlace(new Place($id, $coordinates, $text));
        }


        // Parse refplaces
        $refplaces = $doc->getElementsByTagName('referencePlace');
        foreach ($refplaces as $refplace) {
            //get the id via attribute
            $id = $refplace->getAttribute('id');
            //get the coordinates via tag
            $positionX = $refplace->getElementsByTagName('position')->item(0)->getAttribute('x');
            $positionY = $refplace->getElementsByTagName('position')->item(0)->getAttribute('y');    
            $coordinates = array($positionX, $positionY);
            //get the text via xpath
            $text = $refplace->getAttribute('ref');            
            //add the place
            $builder->addPlace(new Place($id, $coordinates, $text[0]));

            
        }

        // Parse transitions
        $transitions = $doc->getElementsByTagName('transition');
        foreach ($transitions as $transition) {
            //get the id via attribute
            $id = $transition->getAttribute('id'); 
            //get the coordinates via tag 
            $positionX = $transition->getElementsByTagName('position')->item(0)->getAttribute('x');
            $positionY = $transition->getElementsByTagName('position')->item(0)->getAttribute('y');    
            $coordinates = array($positionX, $positionY);
            //get the text 
            $text = $transition->getElementsByTagName('text')[0]->nodeValue;
            //add the transition
            $builder->addTransition(new Transition($id, $coordinates, $text));
        }

        // Parse flows
        $flows = $doc->getElementsByTagName('arc');
        foreach ($flows as $flow) {
            $sourceID = $flow->getAttribute('source');
            $targetID = $flow->getAttribute('target');
            $weight = intval($flow->getElementsByTagName('text')[0]->nodeValue);
            if($weight == 0){
                $weight = 1;
            }

            $target = $builder->getPlace($targetID) ?? $builder->getTransition($targetID);
            $source = $builder->getTransition($sourceID) ?? $builder->getPlace($sourceID);

            if ($place !== NULL && $transition !== NULL) {
                $builder->addFlow(new Flow($source, $target), $weight);
            }
        }

        $petrinet = $builder->getPetrinet();

        // Parse initial marking
        $mBuilder = new MarkingBuilder();
        $places = $doc->getElementsByTagName('place');
        foreach ($places as $place) {
            $marking = count($place->getElementsByTagName('token'));
            if ($marking == 0){continue;} //if there is no marking, skip the loop
            $placeId = $place->getAttribute('id');
            $petriPlace = $builder->getPlace($placeId);
            $mBuilder->assign($petriPlace, new IntegerTokenCount($marking));

        $initialMarkingFinal = $mBuilder->getMarking($petrinet);
        }
        return new MarkedPetrinet($petrinet, $initialMarkingFinal);
    }
}