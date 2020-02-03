<?php

namespace Cora\Handlers;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;

abstract class AbstractHandler implements HandlerInterface {
    protected $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args) {
        return $this->handle($request, $response, $args);
    }
}
