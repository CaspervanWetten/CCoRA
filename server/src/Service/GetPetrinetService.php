<?php

namespace Cora\Service;

use Cora\Domain\Petrinet\Marking\MarkingBuilder;
use Cora\Domain\Petrinet\MarkedPetrinet;
use Cora\Exception\NotFoundException;

use Cora\Repository\PetrinetRepository;

class GetPetrinetService {
    protected $repository;

    public function __construct(PetrinetRepository $repo) {
        $this->repository = $repo;
    }

    public function get($pid, $mid = NULL) {
        $pid = filter_var($pid, FILTER_SANITIZE_NUMBER_INT);
        $mid = is_null($mid) ? NULL : filter_var($mid, FILTER_SANITIZE_NUMBER_INT);
        $petrinet = $this->repository->getPetrinet($pid);

        if (is_null($petrinet))
            return NULL;
        if (!is_null($mid)) {
            $marking = $this->repository->getMarking($mid, $petrinet);
            if (is_null($marking)) {
                throw new NotFoundException("Could not find marking with " .
                                            "id=$mid for Petri net with " .
                                            "id=$pid");
            }
        }
        if (is_null($mid) || is_null($marking))
            $marking = $this->getEmptyMarking($petrinet);

        return new MarkedPetrinet($petrinet, $marking);
    }

    private function getEmptyMarking($petrinet) {
        $builder = new MarkingBuilder();
        return $builder->getMarking($petrinet);
    }
}
