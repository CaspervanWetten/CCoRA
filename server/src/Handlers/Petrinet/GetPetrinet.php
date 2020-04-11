<?php

namespace Cora\Handlers\Petrinet;

use Cora\Domain\Systems\Petrinet\MarkedPetrinet;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cora\Handlers\AbstractHandler;
use Cora\Repositories\PetrinetRepository as PetrinetRepo;

use Exception;

class GetPetrinet extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        if (!isset($args["id"]))
            throw new Exception("No id supplied");
        $id = intval(filter_var($args["id"], FILTER_SANITIZE_NUMBER_INT));
        $repo = $this->container->get(PetrinetRepo::class);
        $petrinet = $repo->getPetrinet($id);
        if (is_null($petrinet)) 
            throw new Exception("The Petri net could not be found");
        $markings = $repo->getMarkings($id);
        if (!empty($markings)) {
            $marking = $repo->getMarking($markings[0]["id"], $petrinet);
            $marked = new MarkedPetrinet($petrinet, $marking);
            return $response->getBody()->write(json_encode($marked));
        }
        return $response->getBody()->write(json_encode($petrinet));
    }
}
