<?php

namespace Cora\Handlers\Petrinet;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Repositories\PetrinetRepository as PetrinetRepo;
use Cora\Services\GetPetrinetImageService;

class GetPetrinetImage extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $id = $args["id"];
        $repo = $this->container->get(PetrinetRepo::class);
        $service = $this->container->get(GetPetrinetImageService::class);
        $image = $service->get($id, $repo);
        $response->getBody()->write($image);
        return $response->withHeader("Content-type", "image/svg+xml");
    }
}
