<?php

namespace Cora\Handlers\Petrinet;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Repositories\PetrinetRepository as PetrinetRepo;
use Cora\Services\GetPetrinetImageService;
use Cora\Views\SvgImageView;

class GetPetrinetImage extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $id = $args["id"];
        $repo = $this->container->get(PetrinetRepo::class);
        $service = $this->container->get(GetPetrinetImageService::class);
        $view = new SvgImageView();
        $service->get($view, $id, $repo);
        return $response->withHeader("Content-type", $view->getContentType())
                        ->withStatus(200)
                        ->write($view->render());
    }
}
