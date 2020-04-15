<?php

namespace Cora\Services;

use Cora\Converters\PetrinetToDot;
use Cora\Domain\Systems\Petrinet\PetrinetInterface as IPetrinet;
use Cora\Repositories\PetrinetRepository as PetriRepo;

use Exception;

class GetPetrinetImageService {
    public function get($id, PetriRepo $petriRepo) {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        if (!$petriRepo->petrinetExists($id))
            throw new Exception("A Petri net with this id does not exist");
        $petrinet = $petriRepo->getPetrinet($id);
        $image = $this->generateImage($petrinet);
        return $image;
    }

    protected function generateImage(IPetrinet $petrinet) {
        $converter = new PetrinetToDot($petrinet);
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
