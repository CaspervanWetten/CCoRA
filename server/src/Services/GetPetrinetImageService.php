<?php

namespace Cora\Services;

use Cora\Converters\PetrinetToDot;
use Cora\Domain\Petrinet\PetrinetInterface as IPetrinet;
use Cora\Domain\Petrinet\PetrinetNotFoundException;
use Cora\Domain\Petrinet\PetrinetRepository as PetriRepo;
use Cora\Domain\Petrinet\Marking\MarkingInterface as IMarking;
use Cora\Views\ImageViewInterface as View;

use Exception;

class GetPetrinetImageService {
    public function get(
        View &$view,
        int $pid,
        ?int $mid,
        PetriRepo $petriRepo
    ) {
        $pid = filter_var($pid, FILTER_SANITIZE_NUMBER_INT);
        if (!$petriRepo->petrinetExists($pid))
            throw new PetrinetNotFoundException(
                "A Petri net with this id does not exist");
        $mid = filter_var($mid, FILTER_SANITIZE_NUMBER_INT);
        $marking = NULL;
        $petrinet = $petriRepo->getPetrinet($pid);
        if ($petriRepo->markingExists($mid))
            $marking = $petriRepo->getMarking($mid, $petrinet);
        $image = $this->generateImage($petrinet, $marking);
        $view->setData($image);
    }

    protected function generateImage(IPetrinet $petrinet, ?IMarking $marking) {
        $converter = new PetrinetToDot($petrinet, $marking);
        $command = sprintf('echo %s | %s -Tsvg',
                           escapeshellarg($converter->convert()),
                           escapeshellcmd(DOT_PATH));
        exec($command, $lines, $status);
        if ($status != 0)
            throw new Exception("Dot exited with non-zero status");
        $res = implode($lines);
        return $res;
    }
}
