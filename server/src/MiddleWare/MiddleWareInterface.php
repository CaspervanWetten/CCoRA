<?php

namespace Cora\MiddleWare;

use Slim\Http\Request;
use Slim\Http\Response;

interface MiddleWareInterface {
    public function __invoke(
        Request $request,
        Response $response,
        callable $next): Response;
}
