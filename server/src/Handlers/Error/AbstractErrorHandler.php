<?php

namespace Cora\Handlers\Error;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Container as Container;

use Exception;

abstract class AbstractErrorHandler implements ErrorHandlerInterface {
    protected $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function __invoke(
        Request $request,
        Response $response,
        Exception $exception
    ): Response {
        return $this->handle($request, $response, $exception);
    }
}
