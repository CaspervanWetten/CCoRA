<?php

namespace Cora\Handlers;

use Cora\Views\ErrorViewFactory;

use Slim\Http\Request;
use Slim\Http\Response;
use Psr\Container\ContainerInterface as Container;

use Exception;

abstract class AbstractRequestHandler extends AbstractHandler {
    public function __construct(Container $container) {
        parent::__construct($container);
    }

    public function __invoke(Request $request, Response $response, $args) {
        try {
            return $this->handle($request, $response, $args);
        } catch (BadRequestException $e) {
            return $this->fail($request, $response, $e, 400);
        }
    }

    public abstract function handle(Request $req, Response $res, $args);

    protected function getErrorMediaType(Request $request): string {
        $supported = $this->getSupportedErrorMediaTypes();
        return $this->negotiateType($request, $supported);
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

    protected function getSupportedErrorMediaTypes(): array {
        $factory = $this->getErrorViewFactory();
        return $factory->getMediaTypes();
    }

    protected function getErrorViewFactory(): ErrorViewFactory {
        return new ErrorViewFactory();
    }
}
