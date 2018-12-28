<?php

namespace Cozp\MiddleWare;

use \Psr\Http\Message\RequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

abstract class MiddleWare
{
    abstract public function __invoke(
        Request $request,
        Response $response,
        callable $next
    );
}

 ?>
