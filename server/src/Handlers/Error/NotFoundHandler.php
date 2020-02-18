<?php

namespace Cora\Handlers\Error;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class NotFoundHandler extends AbstractHttpErrorHandler {
    public function handle(Request $request, Response $response): Response {
        return $response->withJson("The requested resource could not be found");
    }

    public function getErrorCode(): int {
        return 404;
    }
}

