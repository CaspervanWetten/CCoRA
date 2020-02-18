<?php

namespace Cora\Handlers\Error;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

interface HttpErrorHandlerInterface {
    public function handle(Request $request, Response $response): Response;
}
