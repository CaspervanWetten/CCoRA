<?php

namespace Cora\Handlers\Error;

use Slim\Http\Request;
use Slim\Http\Response;

use Exception;

interface ErrorHandlerInterface {
    public function handle(
        Request $request,
        Response $response,
        Exception $exception
    ): Response;
}
    
