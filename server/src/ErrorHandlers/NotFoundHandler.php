<?php

namespace Cora\ErrorHandlers;

use \Cora\Exceptions\CoraException as CoraException;

use \Psr\Http\Message\RequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class NotFoundHandler extends PredefinedErrorHandler
{
    public function __invoke(Request $request, Response $response)
    {
        throw new CoraException("The requested resource could not be found", 404);
    }
}

?>
