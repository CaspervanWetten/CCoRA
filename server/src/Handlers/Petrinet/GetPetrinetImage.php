<?php

namespace Cora\Handlers\Petrinet;

use Cora\Domain\Systems\Petrinet\PetrinetNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Repositories\PetrinetRepository as PetrinetRepo;
use Cora\Services\GetPetrinetImageService;
use Cora\Views\PetrinetImageViewFactory;

class GetPetrinetImage extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        try {
            $id        = $args["id"];
            $repo      = $this->container->get(PetrinetRepo::class);
            $service   = $this->container->get(GetPetrinetImageService::class);
            $mediaType = $this->getMediaType($request);
            $view      = $this->getView($mediaType);
            $service->get($view, $id, $repo);
            return $response->withHeader("Content-type", $mediaType)
                            ->withStatus(200)
                            ->write($view->render());
        } catch (PetrinetNotFoundException $e) {
            $mediaType = $this->getErrorMediaType($request);
            $view      = $this->getErrorView($mediaType);
            $view->setException($e);
            return $response->withHeader("Content-type", $mediaType)
                            ->withStatus(404)
                            ->write($view->render());
        }
    }

    protected function getViewFactory(): \Cora\Views\AbstractViewFactory {
        return new PetrinetImageViewFactory();
    }
}
