<?php

namespace Cora\Handlers\Petrinet;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Domain\Systems\Petrinet\PetrinetRepository as PetrinetRepo;
use Cora\Services\GetPetrinetService;
use Cora\Views\PetrinetViewFactory;
use Cora\Domain\Systems\Petrinet\PetrinetNotFoundException;

class GetPetrinet extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        try {
            $petriRepo = $this->container->get(PetrinetRepo::class);
            $mediaType = $this->getMediaType($request);
            $view      = $this->getView($mediaType);
            $service   = $this->container->get(GetPetrinetService::class);
            $service->get($view, $args["id"], $petriRepo);
            return $response->withHeader("Content-type", $mediaType)
                            ->withStatus(200)
                            ->write($view->render());
        } catch (PetrinetNotFoundException $e) {
            $mediaType = $this->getErrorMediaType($request);
            $view = $this->getErrorView($mediaType);
            $view->setException($e);
            return $response->withHeader("Content-type", $mediaType)
                            ->withStatus(404)
                            ->write($view->render());
        }
    }

    protected function getViewFactory(): \Cora\Views\AbstractViewFactory {
        return new PetrinetViewFactory();
    }
}
