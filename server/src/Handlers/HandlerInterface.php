<?php

namespace Cora\Handlers;

use Slim\Http\Request;
use Slim\Http\Response;

interface HandlerInterface {
    public function handle(Request $request, Response $response, $args);
}
