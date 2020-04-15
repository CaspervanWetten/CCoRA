<?php

namespace Cora\Handlers\Petrinet;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Repositories\PetrinetRepository as PetrinetRepo;
use Cora\Services\GetPetrinetsService;

class GetPetrinets extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $repo = $this->container->get(PetrinetRepo::class);
        $service = $this->container->get(GetPetrinetsService::class);
        $limit = $args["limit"];
        $page = $args["page"];
        $petrinets = $service->get($page, $limit, $repo);
        return $response->withJson($petrinets);
    }
}
