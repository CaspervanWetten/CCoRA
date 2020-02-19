<?php

namespace Cora\Handlers\Error;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response; 
use Psr\Container\ContainerInterface as Container;

abstract class AbstractHttpErrorHandler implements HttpErrorHandlerInterface {
    protected $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public abstract function handle(Request $request, Response $response): Response;

    public abstract function getErrorCode(): int;

    public function __invoke(Request $request, Response $response): Response {
        return $this->handle($request, $response)
                    ->withStatus($this->getErrorCode());
    }
}