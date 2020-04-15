<?php

namespace Cora\Handlers;

use Slim\Http\Request;
use Slim\Http\Response;
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
