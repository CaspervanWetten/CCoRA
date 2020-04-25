<?php

namespace Cora\Handlers\Error;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Handlers\AbstractHandler;
use Cora\Views\ErrorViewFactory;
use Psr\Container\ContainerInterface;

use Exception;

class ErrorHandler extends AbstractHandler {
    public function __construct(ContainerInterface $container) {
        parent::__construct($container);
    }

    public function __invoke(
        Request   $request,
        Response  $response,
        Exception $e
    ): Response {
        return $this->handle($request, $response, $e);
    }

    protected function handle(
        Request   $request,
        Response  $response,
        Exception $e
    ): Response {
        $mediaType = $this->getMediaType($request);
        $view = $this->getView($mediaType);
        $view->setException($e);
        return $response->withHeader("Content-type", $mediaType)
                        ->withStatus(500)
                        ->write($view->render());
    }

    protected function getViewFactory(): \Cora\Views\AbstractViewFactory {
        return new ErrorViewFactory();
    }
}
