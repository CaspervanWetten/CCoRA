<?php

namespace Cozp\Systems;

class CoverabilityMarking extends Marking
{
    public $reachableFromInitial;

    public function __construct($petrinet, $list = [], $reachable = false)
    {
        parent::__construct($petrinet, $list);
        $this->reachableFromInitial = $reachable;
    }

    /**
     * Convert a CoverabilityMarking to a standard marking
     *
     * @param Petrinet $petrinet
     * @return Marking
     */
    public function asMarking($petrinet)
    {
        $m = new Marking($petrinet, $this->vector);
        return $m;
    }
}

?>