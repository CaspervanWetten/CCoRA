<?php

namespace Cora\Handlers;

use Cora\Views\AbstractViewFactory;
use Slim\Http\Request;
use Psr\Container\ContainerInterface as Container;

use Negotiation\Negotiator;
use Exception;

abstract class AbstractHandler {
    protected $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }

    protected function getMediaType(Request $request): string {
        $supported = $this->getSupportedMediaTypes();
        return $this->negotiateType($request, $supported);
    }

    protected function getView(string $mediaType) {
        $factory = $this->getViewFactory();
        return $factory->create($mediaType);
    }

    protected function negotiateType(Request $request, array $supported): string {
        $clientAccept = $request->getHeaderLine("Accept");
        if (empty($clientAccept))
            $clientAccept = "*/*";
        $negatotiator = new Negotiator();
        $type = $negatotiator->getBest($clientAccept, $supported);
        if (is_null($type))
            throw new Exception("Could not provide acceptable media format");
        return $type->getType();
    }

    protected function getSupportedMediaTypes(): array {
        $factory = $this->getViewFactory();
        return $factory->getMediaTypes();
    }

    protected abstract function getViewFactory(): AbstractViewFactory;
}
