<?php

namespace Cora\Handlers\Petrinet;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Repositories\PetrinetRepository as PetrinetRepo;
use Cora\Services\GetPetrinetService;
use Cora\Views\JsonErrorView;
use Cora\Views\PetrinetViewFactory;
use Exception;

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
        } catch (Exception $e) {
            $view = new JsonErrorView($e);
            return $response->withHeader("Content-type", $mediaType)
                            ->withStatus(404)
                            ->write($view->render());
        }
    }

    protected function getViewFactory(): \Cora\Views\AbstractViewFactory {
        return new PetrinetViewFactory();
    }
}
