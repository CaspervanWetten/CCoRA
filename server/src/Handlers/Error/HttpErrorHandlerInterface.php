<?php

namespace Cora\Handlers\Error;

use Slim\Http\Request;
use Slim\Http\Response;

interface HttpErrorHandlerInterface {
    public function handle(Request $request, Response $response): Response;
}
