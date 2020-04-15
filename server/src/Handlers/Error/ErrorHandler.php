<?php

namespace Cora\Handlers\Error;

use Slim\Http\Request;
use Slim\Http\Response;

use Throwable;

class ErrorHandler extends AbstractErrorHandler {
    public function handle(
        Request $request,
        Response $response,
        Throwable $exception
    ): Response {
        return $response->withJson($exception->getMessage())
                        ->withStatus(500);
    }
}
