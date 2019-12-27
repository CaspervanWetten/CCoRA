<?php

namespace Cora\ErrorHandlers;

use \Psr\Http\Message\RequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \Exception as Exception;

abstract class Errorhandler
{
    public abstract function __invoke(Request $request, Response $response, Exception $exception);
}

?>
