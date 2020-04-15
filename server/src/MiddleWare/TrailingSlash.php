<?php

namespace Cora\MiddleWare;

use Slim\Http\Request;
use Slim\Http\Response;

class TrailingSlash implements MiddleWareInterface {
    const ADD_TRAILING_SLASH = 1;
    const REMOVE_TRAILING_SLASH= 2;

    protected $option;
    public function __construct(int $option) {
        $this->option = $option;
    }

    public function __invoke(
        Request $request,
        Response $response,
        callable $next): Response
    {
        switch ($this->option) {
        case self::ADD_TRAILING_SLASH:
                return $this->addTrailingSlash($request, $response, $next);
                break;
            case self::REMOVE_TRAILING_SLASH:
                return $this->removeTrailingSlash($request, $response, $next);
                break;
        }
    }

    protected function addTrailingSlash(
        Request $request,
        Response $response,
        callable $next)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        if(substr($path, -1) != "/")
        {
            $uri = $uri->withPath($path . '/');
            if($request->isGet())
                return $response->withRedirect((string)$uri, 301);
            return $next($request->withUri($uri), $response);
        }
        return $next($request, $response);
    }

    private function removeTrailingSlash(
        Request $request,
        Response $response,
        callable $next)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        if ($path != '/' && substr($path, -1) == '/')
        {
            $uri = $uri->withPath(substr($path, 0, -1));
            if($request->isGet())
                return $response->withRedirect((string)$uri, 301);
            return $next($request->withUri($uri), $response);
        }
        return $next($request, $response);
    }
}
