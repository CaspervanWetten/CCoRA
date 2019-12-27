<?php

namespace Cora\ErrorHandlers;

use \Psr\Http\Message\RequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

abstract class PredefinedErrorHandler
{
    public abstract function __invoke(Request $request, Response $response);
}
