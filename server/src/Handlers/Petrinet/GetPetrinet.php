<?php

namespace Cora\Handlers\Petrinet;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cora\Handlers\AbstractHandler;
use Cora\Repositories\PetrinetRepository as PetrinetRepo;
use Cora\Converters\Petrinet2ToJson;

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
        $converter = new Petrinet2ToJson($petrinet);
        $p = $converter->convert();
        return $response->getBody()->write($p);
    }
}
