<?php

namespace Cora\Handlers\Error;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Views\AbstractViewFactory;
use Cora\Views\NotAllowedViewFactory;
use Psr\Container\ContainerInterface;

class NotAllowedHandler extends AbstractHandler {
    public function __construct(ContainerInterface $container) {
        parent::__construct($container);
    }

    public function __invoke(Request $request, Response $response, $methods) {
        return $this->handle($request, $response, $methods);
    }

    protected function handle(Request $request, Response $response, $methods) {
        $usedMethod = $request->getMethod();
        $mediaType = $this->getMediaType($request);
        $view = $this->getView($mediaType);
        $view->setUsedMethod($usedMethod);
        $view->setAllowedMethods($methods);
        return $response->withHeader("Content-type", $mediaType)
                        ->withStatus(405)
                        ->write($view->render());
    }

    protected function getViewFactory(): AbstractViewFactory {
        return new NotAllowedViewFactory();
    }
}
