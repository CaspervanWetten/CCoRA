<?php

namespace Cora\Handlers\Error;

use Slim\Http\Request;
use Slim\Http\Response;

class NotFoundHandler extends AbstractHttpErrorHandler {
    public function handle(Request $request, Response $response): Response {
        return $response->withJson("The requested resource could not be found");
    }

    public function getErrorCode(): int {
        return 404;
    }
}

