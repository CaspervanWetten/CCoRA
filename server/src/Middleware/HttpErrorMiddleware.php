<?php

namespace Cora\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\Middleware\ErrorMiddleware;
use Slim\Exception\HttpException;

use Throwable;

class HttpErrorMiddleware extends ErrorMiddleware {
    public function handleException(Request $request, Throwable $exception): Response {
        if ($exception->getCode() >= 400 && $exception->getCode() < 600) {
            $exception = new HttpException(
                $request,
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
            $exception->setTitle($exception->getMessage());
        }

        return parent::handleException($request, $exception);
    }
}
