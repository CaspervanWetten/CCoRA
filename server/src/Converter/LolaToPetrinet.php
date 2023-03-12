<?php

namespace Cora\Converter;

use Cora\Domain\Petrinet\PetrinetBuilder;
use Cora\Domain\Petrinet\Place\Place;
use Cora\Domain\Petrinet\Transition\Transition;
use Cora\Domain\Petrinet\Flow\Flow;
use Cora\Domain\Petrinet\Marking\Tokens\IntegerTokenCount;
use Cora\Domain\Petrinet\Marking\MarkingBuilder;
use Cora\Domain\Petrinet\MarkedPetrinet;

use Cora\Exception\BadInputException;

class LolaToPetrinet extends Converter {
    protected $lola;

    public function __construct(string $lola) {
        $this->lola = $lola;
    }

    public function convert() {
        $lines = [];
        foreach(explode("\n", trim($this->lola)) as $line) {
            $l = trim($line);
            if (!empty($l))
                array_push($lines, $l);
        }
        $markingLine = NULL;
        $builder = new PetrinetBuilder();
        foreach ($lines as $i => $line) {
            if(preg_match('/PLACE/i', $line))
                $this->parsePlaces($line, $builder);

            if(preg_match('/MARKING/i', $line))
                $markingLine = $line;

            if(preg_match('/TRANSITION/i', $line)) {
                $sub = [$line, $lines[++$i], $lines[++$i]];
                $this->parseTransition($sub, $builder);
            }
        }
        $petrinet = $builder->getPetrinet();
        if (is_null($markingLine))
            throw new BadInputException("Could not parse Lola: no marking");
        $marking = $this->parseMarking($markingLine, $petrinet);
        return new MarkedPetrinet($petrinet, $marking);
    }

    protected function parsePlaces($line, &$builder) {
        $s = trim(preg_replace('/PLACE|[,;]/i', '', $line));
        $s = explode(' ', $s);
        foreach ($s as $name)
            $builder->addPlace(new Place($name));
    }

    protected function parseMarking($line, &$petrinet) {
        $builder = new MarkingBuilder();
        $m = trim(preg_replace('/MARKING|[ ;]/i', '', $line));
        $m = explode(',', $m);
        foreach ($m as $item) {
            list($place, $tokens) = sscanf($item, "%[^:]:%d");
            $builder->assign(new Place($place), new IntegerTokenCount($tokens));
        }
        return $builder->getMarking($petrinet);
    }

    protected function parseTransition(array $lines, PetrinetBuilder &$builder) {
        $line = $lines[0];
        list($trans) = sscanf($line, "TRANSITION %s");
        $builder->addTransition(new Transition($trans));
        $line = $lines[1];
        $c = trim(preg_replace('/CONSUME|[ ;]/i', '', $line));
        $c = preg_split("/,/", $c, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($c as $cons) {
            list($from, $amount) = sscanf($cons, "%[^:]:%d");
            $flow = new Flow(new Place($from), new Transition($trans));
            $builder->addFlow($flow, $amount);
        }
        $line = $lines[2];
        $p = trim(preg_replace('/PRODUCE|[ ;]/i', '', $line));
        $p = preg_split("/,/", $p, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($p as $prod) {
            list($to, $amount) = sscanf($prod, "%[^:]:%d");
            $flow = new Flow(new Transition($trans), new Place($to));
            $builder->addFlow($flow, $amount);
        }
    }
}
