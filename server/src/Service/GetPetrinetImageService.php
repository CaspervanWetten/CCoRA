<?php

namespace Cora\Service;

use Cora\Converter\PetrinetToDot;
use Cora\Domain\Petrinet\PetrinetInterface as IPetrinet;
use Cora\Domain\Petrinet\Marking\MarkingInterface as IMarking;
use Cora\Repository\PetrinetRepository;

use Cora\Exception\NotFoundException;

class GetPetrinetImageService {
    private $repository;

    public function __construct(PetrinetRepository $repository) {
        $this->repository = $repository;
    }

    public function get(int $pid, ?int $mid) {
        $pid = filter_var($pid, FILTER_SANITIZE_NUMBER_INT);
        if (!$this->repository->petrinetExists($pid))
            throw new NotFoundException(
                "A Petri net with id=$pid does not exist");
        $mid = filter_var($mid, FILTER_SANITIZE_NUMBER_INT);
        $marking = NULL;
        $petrinet = $this->repository->getPetrinet($pid);

        if (!is_null($mid)) {
            $marking = $this->repository->getMarking($mid, $petrinet);
            if (is_null($marking))
                throw new NotFoundException(
                    "Could not find marking with id=$mid");
        }

        return $this->generateImage($petrinet, $marking);
    }

    protected function generateImage(IPetrinet $petrinet, ?IMarking $marking) {
        $converter = new PetrinetToDot($petrinet, $marking);
        $command = sprintf('echo %s | %s -Tsvg',
                           escapeshellarg($converter->convert()),
                           escapeshellcmd($_ENV['DOT_PATH']));
        exec($command, $lines, $status);
        if ($status != 0)
            throw new Exception("Dot exited with non-zero status");
        $res = implode($lines);
        return $res;
    }
}
