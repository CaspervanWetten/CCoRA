<?php

namespace Cozp\ErrorHandlers;

use \Psr\Http\Message\RequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \Exception as Exception;

class NotFoundHandler extends PreDefinedErrorHandler
{
    public function __invoke(Request $request, Response $response)
    {
        $handler = new JSONErrorHandler();
        $response = $handler(
            $request,
            $response,
            new \Exception("The requested resource could not be found.")
        );

        return $response->withStatus(404);
    }
}

?>