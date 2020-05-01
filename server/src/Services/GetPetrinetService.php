<?php

namespace Cora\Services;

use Cora\Domain\Petrinet\Marking\MarkingNotFoundException;
use Cora\Domain\Petrinet\PetrinetNotFoundException;
use Cora\Domain\Petrinet\PetrinetRepository as PetriRepo;
use Cora\Domain\Petrinet\View\MarkedPetrinetViewInterface as View;

class GetPetrinetService {
    public function get(View &$view, $pid, $mid, PetriRepo $petriRepo) {
        $pid = filter_var($pid, FILTER_SANITIZE_NUMBER_INT);
        $mid = is_null($mid) ? NULL : filter_var($mid, FILTER_SANITIZE_NUMBER_INT);
        $petrinet = $petriRepo->getPetrinet($pid);
        if (is_null($petrinet))
            throw new PetrinetNotFoundException(
                "The Petri net could not be found");
        $marking = is_null($mid) ? NULL : $petriRepo->getMarking($mid, $petrinet);
        if (is_null($marking) && !is_null($mid))
            throw new MarkingNotFoundException(
                "The marking could not be found");
        $view->setPetrinet($petrinet);
        $view->setMarking($marking);
    }
}
