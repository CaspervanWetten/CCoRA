<?php

namespace Cozp\Controllers;

use Cozp\Utils as Utils;
use \Psr\Http\Message\RequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

abstract class Controller
{
    protected $container;
    protected static $instance;

    public static final function getInstance(&$container)
    {
        if(!isset(static::$instance))
            static::$instance = new static($container);
        return static::$instance;
    }

    protected function __construct(&$container)
    {
        $this->container = $container;
    }

    /**
     * Redirect the request to the 404 page.
     * @param  Request  $request  The current request object
     * @param  Response $response The current response object
     * @return Response           The new response to the 404 page.
     */
    protected function show404(Request $request, Response $response)
    {
        $handler = $this->container->get('notFoundHandler');
        return $handler($request, $response);
    }
    
    /**
     * Move a file to the supplied directory and return its (new) file name
     * @param  UploadedFile $file   The uploaded file object
     * @param  string $directory    The directory where the file needs to be
     * @return string               The new file name
     */
    protected static function moveUploadedFile($file, $directory)
    {
        if (!is_dir($directory) && !file_exists($directory))
            Utils\FileUtils::mkdir($directory);

        $filename = $file->getClientFilename();
        $file->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
        return $filename;
    }
}

 ?>
