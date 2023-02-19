<?php

namespace Cora\Handler;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;
use Negotiation\Negotiator;

use Cora\View\Factory\ViewFactory;
use Cora\View\Factory\ErrorViewFactory;
use Cora\Exception\HttpNotAcceptableException;

abstract class AbstractHandler {
    protected $container;
    private $view;
    private $type;

    public function __construct(Container $container) {
        $this->container = $container;
        $this->view = NULL;
        $this->type = NULL;
    }

    public function __invoke(Request $request, Response $response, $args) {
        return $this->handleRequest($request, $response, $args);
    }

    public function handleRequest(Request $request, Response $response, $args) {
        $this->setViewFor($request);
        $response = $response->withHeader(
            'Content-Type', $this->getContentType());

        return $this->handle($request, $response, $args);
    }

    public abstract function handle(Request $req, Response $res, $args);

    public function getView() {
        return $this->view;
    }

    protected abstract function getViewFactory(): ViewFactory;

    private function setViewFor($request) {
        $this->type = $this->negotiateContentType($request);
        $this->view = $this->getViewFactory()->create($this->type);
    }

    private function negotiateContentType($request) {
        $viewFactory = $this->getViewFactory();

        $negotiator = new Negotiator();
        $accept = $request->getHeaderLine('Accept');
        if (empty($accept))
            $accept = "*/*";
        $available = $viewFactory->getContentTypes();
        $agreement = $negotiator->getBest($accept, $available);

        if (is_null($agreement))
            throw new HttpNotAcceptableException(
                $request, 'Cannot provide acceptable content type');

        return $agreement->getType();
    }

    private function getContentType() {
        return $this->type;
    }
}
