<?php

namespace Cora\MiddleWare;

use \Cora\Enumerators\TrailingSlashOptions as TrailingSlashOptions;
use \Psr\Http\Message\RequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class TrailingSlash extends MiddleWare
{
    private $option;
    public function __construct($option)
    {
        $this->option = $option;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        switch ($this->option) {
            case TrailingSlashOptions::ADD_TRAILING_SLASH:
                return $this->addTrailingSlash($request, $response, $next);
                break;

            case TrailingSlashOptions::REMOVE_TRAILING_SLASH:
                return $this->removeTrailingSlash($request, $response, $next);
                break;
        }
    }

    private function addTrailingSlash(Request $request, Response $response, callable $next)
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

    private function removeTrailingSlash(Request $request, Response $response, callable $next)
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

?>
