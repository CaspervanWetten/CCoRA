<?php

namespace Cora\Handlers;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

interface HandlerInterface {
    public function handle(Request $request, Response $response, $args);
}
