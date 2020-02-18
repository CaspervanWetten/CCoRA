<?php

namespace Cora\Handlers;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Psr\Container\ContainerInterface;

use Throwable;

abstract class AbstractErrorHandler {
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    
    public abstract function handle(
        Request   $request,
        Response  $response,
        Throwable $exception);

    public function __invoke(Request $req, Response $res, Throwable $e) {
        return $this->handle($req, $res, $e);
    }
}
