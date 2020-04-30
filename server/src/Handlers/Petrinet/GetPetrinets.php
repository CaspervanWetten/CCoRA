<?php

namespace Cora\Handlers\Petrinet;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Domain\Petrinet\PetrinetRepository as PetrinetRepo;
use Cora\Domain\Petrinet\View\PetrinetsViewFactory;
use Cora\Handlers\AbstractRequestHandler;
use Cora\Services\GetPetrinetsService;

class GetPetrinets extends AbstractRequestHandler {
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
