<?php

namespace Cora\Handlers\Petrinet;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Repositories\PetrinetRepository as PetrinetRepo;
use Cora\Services\GetPetrinetService;

class GetPetrinet extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $petriRepo = $this->container->get(PetrinetRepo::class);
        $service = $this->container->get(GetPetrinetService::class);
        $petrinet = $service->get($args["id"], $petriRepo);
        return $response->getBody()->write(json_encode($petrinet));
    }
}
