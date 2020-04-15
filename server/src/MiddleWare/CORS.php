<?php

namespace Cora\MiddleWare;

use Slim\Http\Request;
use Slim\Http\Response;

class CORS implements MiddleWareInterface {
    protected $allow;

    public function __construct(string $allow="*") {
        $this->allow = $allow;
    }

    public function __invoke(
        Request $request,
        Response $response,
        callable $next): Response
    {
        $response = $next($request, $response);
        $newResponse = $response->withHeader(
            "Access-Control-Allow-Origin",
            $this->allow);
        return $newResponse;
    }
}
