<?php

namespace Cora\Handlers\Error;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
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
