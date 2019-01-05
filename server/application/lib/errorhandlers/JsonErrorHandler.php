<?php

namespace Cora\ErrorHandlers;

use \Psr\Http\Message\RequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \Cora\Exceptions\CoraException as CoraException;

class JSONErrorHandler
{
    public function __invoke(Request $request, Response $response, \Exception $exception=null)
    {
        $message;
        if(!is_null($exception)){
            $message = $exception->getMessage();
        } else {
            $message = "Unknown exception.";
        }
        
        $json = array();
        $json["error"] = $message;

        $statusCode = 500;
        if($exception instanceof CoraException) {
            $statusCode = $exception->getHttpStatus();
        }

        return $response->withJson($json)->withStatus($statusCode);
    }
}

?>
