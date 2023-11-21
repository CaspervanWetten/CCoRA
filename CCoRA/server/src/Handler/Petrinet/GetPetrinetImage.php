<?php

namespace Cora\Handler\Petrinet;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cora\Handler\AbstractHandler;
use Cora\Service\GetPetrinetImageService;
use Cora\View\Factory\ViewFactory;
use Cora\View\Factory\PetrinetImageViewFactory;

class GetPetrinetImage extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $queryParams = $request->getQueryParams();

        $pid     = $args["petrinet_id"];
        $mid     = $queryParams["marking_id"] ?? NULL;
        $service = $this->container->get(GetPetrinetImageService::class);
        $image   = $service->get($pid, $mid);

        $view = $this->getView();
        $view->setData($image);
        $response->getBody()->write($view->render());
        return $response;
    }

    protected function getViewFactory(): ViewFactory {
        return new PetrinetImageViewFactory();
    }
}
