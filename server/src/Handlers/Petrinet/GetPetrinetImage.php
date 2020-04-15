<?php

namespace Cora\Handlers\Petrinet;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Repositories\PetrinetRepository as PetrinetRepo;
use Cora\Converters\PetrinetToDot;

use Exception;

class GetPetrinetImage extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        if (!isset($args["id"]))
            throw new Exception("Could not retrieve image: no id supplied");
        $id = intval(filter_var($args["id"], FILTER_SANITIZE_NUMBER_INT));
        $repo = $this->container->get(PetrinetRepo::class);
        $petrinet = $repo->getPetrinet($id);
        if (is_null($petrinet)) 
            throw new Exception("A Petri net with this id does not exist");
        $image = $this->generateImage($petrinet);
        $response->getBody()->write($image);
        return $response->withHeader("Content-type", "image/svg+xml");
    }

    protected function generateImage($petrinet) {
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
