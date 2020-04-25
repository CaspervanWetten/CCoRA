<?php

namespace Cora\Handlers;

use Cora\Views\AbstractViewFactory;
use Cora\Views\ErrorViewFactory;
use Slim\Http\Request;
use Slim\Http\Response;
use Psr\Container\ContainerInterface as Container;

use Negotiation\Negotiator;
use Exception;

abstract class AbstractHandler implements HandlerInterface {
    protected $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args) {
        return $this->handle($request, $response, $args);
    }

    public function getMediaType(Request $request): string {
        $supported = $this->getSupportedMediaTypes();
        return $this->negotiateType($request, $supported);
    }

    public function getErrorMediaType(Request $request): string {
        $supported = $this->getSupportedErrorMediaTypes();
        return $this->negotiateType($request, $supported);
    }

    protected function getView(string $mediaType) {
        $factory = $this->getViewFactory();
        return $factory->create($mediaType);
    }

    protected function getErrorView(string $mediaType) {
        $factory = $this->getErrorViewFactory();
        return $factory->create($mediaType);
    }

    protected function fail(
        Request   $request,
        Response  $response,
        Exception $e,
        int       $status=400
    ) {
        $mediaType = $this->getErrorMediaType($request);
        $view = $this->getErrorView($mediaType);
        $view->setException($e);
        return $response->withHeader("Content-type", $mediaType)
                         ->withStatus($status)
                         ->write($view->render());
    }

    protected function negotiateType(Request $request, array $supported): string {
        $clientAccept = $request->getHeaderLine("Accept");
        if (empty($clientAccept))
            $clientAccept = "*/*";
        $negatotiator = new Negotiator();
        $type = $negatotiator->getBest($clientAccept, $supported);
        if (is_null($type))
            throw new Exception("Could not provide acceptable media format");
        return $type->getType();
    }

    protected function getSupportedMediaTypes(): array {
        $factory = $this->getViewFactory();
        return $factory->getMediaTypes();
    }

    protected function getSupportedErrorMediaTypes(): array {
        $factory = $this->getErrorViewFactory();
        return $factory->getMediaTypes();
    }

    protected abstract function getViewFactory(): AbstractViewFactory;

    protected function getErrorViewFactory(): ErrorViewFactory {
        return new ErrorViewFactory();
    }
}
