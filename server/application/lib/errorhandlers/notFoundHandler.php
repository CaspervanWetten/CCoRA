<?php

namespace Cozp\ErrorHandlers;

use Cozp\Exceptions\CozpException as CozpException;
use \Psr\Http\Message\RequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class NotFoundHandler extends PreDefinedErrorHandler
{
    public function __invoke(Request $request, Response $response)
    {
        throw new CozpException("The requested resource could not be found", 404);
    }
}

?>
