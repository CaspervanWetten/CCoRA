<?php

namespace Cora\Exception;

use Slim\Exception\HttpSpecializedException;

class HttpNotAcceptableException extends HttpSpecializedException {
    protected $code = 406;
    protected $message = 'Not Acceptable';
    protected string $title = 'Unacceptable content type';
    protected string $description = 'Cannot not supply media type acceptable to client';
}
