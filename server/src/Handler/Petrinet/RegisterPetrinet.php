<?php

namespace Cora\Handler\Petrinet;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

use Cora\Handler\AbstractHandler;
use Cora\Service\RegisterPetrinetService;
use Cora\View\Factory\ViewFactory;
use Cora\View\Factory\PetrinetCreatedViewFactory;

class RegisterPetrinet extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();

        $userId = $parsedBody["user_id"] ?? NULL;
        if (is_null($userId))
            throw new HttpBadRequestException($request, "No user id supplied");
        $files = $request->getUploadedFiles();
        if (!isset($files["petrinet"]))
            throw new HttpBadRequestException($request, "No Petri net uploaded");
        $file    = $files["petrinet"];

        $service = $this->container->get(RegisterPetrinetService::class);
        $result  = $service->register($file, $userId);

        if ($result->isFailure())
            throw new HttpBadRequestException($request, $result->getError());

        $view = $this->getView();
        $view->setResult($result);
        $response->getBody()->write($view->render());
        return $response->withStatus(201);
    }

    protected function getViewFactory(): ViewFactory {
        return new PetrinetCreatedViewFactory();
    }
}
