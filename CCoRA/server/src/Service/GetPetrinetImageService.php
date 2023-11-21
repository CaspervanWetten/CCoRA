<?php

namespace Cora\Service;

use Cora\Utils\Printer;

use Cora\Converter\PetrinetToDot;
use Cora\Domain\Petrinet\PetrinetInterface as IPetrinet;
use Cora\Domain\Petrinet\Marking\MarkingInterface as IMarking;
use Cora\Repository\PetrinetRepository;

use Cora\Exception\PetrinetNotFoundException;

class GetPetrinetImageService {
    private $repository;
    private $printer;

    public function __construct(PetrinetRepository $repository) {
        $this->repository = $repository;
        $this->printer = new Printer;
    }

    public function get(int $pid, ?int $mid) {
        $pid = filter_var($pid, FILTER_SANITIZE_NUMBER_INT);
        if (!$this->repository->petrinetExists($pid))
            throw new PetrinetNotFoundException(
                "A Petri net with this id does not exist");
        $mid = filter_var($mid, FILTER_SANITIZE_NUMBER_INT);
        $marking = NULL;
        $petrinet = $this->repository->getPetrinet($pid);
        if ($this->repository->markingExists($mid))
            $marking = $this->repository->getMarking($mid, $petrinet);
        return $this->generateImage($petrinet, $marking);
    }

    protected function generateImage(IPetrinet $petrinet, ?IMarking $marking) {
        $converter = new PetrinetToDot($petrinet, $marking);
        $command = sprintf('echo %s | %s -Knop2 -Tsvg',
                           escapeshellarg($converter->convert()),
                           escapeshellcmd('dot'));
        exec($command, $lines, $status);
        if ($status != 0)
            throw new Exception("Dot exited with non-zero status");
        $res = implode($lines);
        return $res;
    }
}
