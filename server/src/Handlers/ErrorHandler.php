<?php

namespace Cora\Handlers;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Throwable;

class ErrorHandler extends AbstractErrorHandler {
    public function handle(
        Request $request,
        Response $response,
        Throwable $exception
    ) {
        $message = $exception->getMessage();
        return $response->withJson($message);
    }
}
