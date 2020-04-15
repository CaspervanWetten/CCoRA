<?php

namespace Cora\Handlers\Petrinet;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Repositories\PetrinetRepository as PetrinetRepo;
use Cora\Services\GetPetrinetService;
use Cora\Views\JsonErrorView;
use Cora\Views\JsonPetrinetView;
use Exception;

class GetPetrinet extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        try {
            $petriRepo = $this->container->get(PetrinetRepo::class);
            $service = $this->container->get(GetPetrinetService::class);
            $view = new JsonPetrinetView();
            $service->get($view, $args["id"], $petriRepo);
            return $response->withHeader("Content-type", $view->getContentType())
                            ->withStatus(200)
                            ->write($view->render());
        } catch (Exception $e) {
            $view = new JsonErrorView($e);
            return $response->withHeader("Content-type", $view->getContentType())
                            ->withStatus(404)
                            ->write($view->render());
        }
    }
}
