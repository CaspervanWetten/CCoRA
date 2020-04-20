<?php

namespace Cora\Handlers\Petrinet;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Repositories\PetrinetRepository as PetrinetRepo;
use Cora\Services\GetPetrinetsService;
use Cora\Views\PetrinetsViewFactory;

class GetPetrinets extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $repo      = $this->container->get(PetrinetRepo::class);
        $service   = $this->container->get(GetPetrinetsService::class);
        $limit     = $args["limit"];
        $page      = $args["page"];
        $mediaType = $this->getMediaType($request);
        $view      = $this->getView($mediaType);
        $service->get($view, $page, $limit, $repo);
        return $response->withHeader("Content-type", $mediaType)
                        ->withStatus(200)
                        ->write($view->render());
    }

    protected function getViewFactory(): \Cora\Views\AbstractViewFactory {
        return new PetrinetsViewFactory();
    }
}
