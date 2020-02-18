<?php

namespace Cora\Handlers\Error;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Exception;

interface ErrorHandlerInterface {
    public function handle(
        Request $request,
        Response $response,
        Exception $exception
    ): Response;
}
    
