<?php

namespace Cora\MiddleWare;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class CORS extends MiddleWare {
    protected $allow;

    public function __construct(string $allow="*") {
        $this->allow = $allow;
    }

    public function __invoke(
        Request $request,
        Response $response,
        callable $next) {
        $response = $next($request, $response);
        $newResponse = $response->withHeader(
            "Access-Control-Allow-Origin",
            $this->allow);
        return $newResponse;
    }
}
