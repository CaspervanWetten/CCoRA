<?php

namespace Cora\Handlers\Error;

use Cora\Handlers\AbstractHandler;
use Cora\Views\AbstractViewFactory;
use Cora\Views\NotFoundViewFactory;
use Psr\Container\ContainerInterface as Container;

use Slim\Http\Request;
use Slim\Http\Response;

class NotFoundHandler extends AbstractHandler {
    public function __construct(Container $container) {
        parent::__construct($container);
    }

    public function __invoke(Request $request, Response $response) {
        return $this->handle($request, $response);
    }

    protected function handle(Request $request, Response $response) {
        $mediaType = $this->getMediaType($request);
        $view = $this->getView($mediaType);
        return $response->withHeader("Content-type", $mediaType)
                        ->withStatus(404)
                        ->write($view->render());
    }

    protected function getViewFactory(): AbstractViewFactory {
        return new NotFoundViewFactory();
    }
}
