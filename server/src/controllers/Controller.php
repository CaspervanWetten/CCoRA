<?php

namespace Cora\Controllers;

use \Cora\Utils as Utils;

use \Psr\Http\Message\RequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

abstract class Controller
{
    protected $container;
    protected static $instance;

    protected function __construct(&$container)
    {
        $this->container = $container;
    }

   /**
    * Controllers implement the singleton pattern. As a consequence,
    * models can only be instantiated once, by hiding the constructor
    * behind a getInstance method. This method creates the controller
    * if it is not instantiated yet, otherwise you'll receive the already
    * instantiated controller.
    * @param \Pimple\Container $container The container for the controller
    * @return Controller The instance for the controller
    **/
    public static final function getInstance(&$container)
    {
        if(!isset(static::$instance))
            static::$instance = new static($container);
        return static::$instance;
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
}

 ?>
