<?php

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use Slim\Factory\AppFactory;
use Slim\Exception\HttpNotFoundException;

use DI\Container;

use Monolog\Logger;
use Monolog\Level as LogLevel;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

use Tuupola\Middleware\CorsMiddleware;
use Cora\Middleware\HttpErrorMiddleware;

use Cora\Handler;
use Cora\Utils;

/**************************************
*               SLIM SETUP            *
**************************************/

$container = new Container();
AppFactory::setContainer($container);

$app = AppFactory::create();

/**
 * Dependency Injection Container (DIC). Holds all objects which may be used by
 * controllers, models and so forth.
 */
$container = $app->getContainer();

$container->set(\PDO::class, function(ContainerInterface $c) {
    $dsn  = $_ENV['DSN'];
    $user = $_ENV['DB_USER'];
    $pass = $_ENV['DB_PASS'];
    return Utils\DatabaseUtils::connect($dsn, $user, $pass);
});

$container->set(LoggerInterface::class, function(ContainerInterface $c) {
    $logger = new Logger('logger');
    $formatter = new LineFormatter();
    $streamHandler = new StreamHandler('log/production.log');
    $streamHandler->setFormatter($formatter);
    $logger->pushHandler($streamHandler);
    return $logger;
});

/**************************************
*               MIDDLEWARE            *
**************************************/

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$errorHandlerMiddleware = new HttpErrorMiddleware(
    $app->getCallableResolver(),
    $app->getResponseFactory(),
    false,
    true,
    true,
    $container->get(LoggerInterface::class)
);
$app->addMiddleware($errorHandlerMiddleware);

$app->addMiddleware(new CorsMiddleware([
    "origin"        => ["*"],
    "methods"       => ["GET", "POST", "PUT", "DELETE", "PATCH", "OPTIONS"],
    "headers.allow" => ["Content-Type", "Accept", "Origin",
                        "X-Requested-With", "Authorization"]
]));

/**************************************
*                ROUTES               *
**************************************/

// Allow an OPTIONS preflight request on all routes
$app->options('/{routes:.+}', function($request, $response, $args) {
    return $response;
});

/**
 * Setup api group
 */
$app->group('/' . $_ENV['API_GROUP'], function($api_group) {
    /**
     * All functions regarding the creation and retrieval of users
     */
    $api_group->group('/' . $_ENV['USER_GROUP'], function($user_group) {
        // get all users
        $user_group->get(
            '/{id:[0-9]+}', Handler\User\GetUser::class
        )->setName('getUser');
        $user_group->get(
            '/[{limit:[0-9]+}/{page:[0-9]+}]', Handler\User\GetUsers::class
        )->setName('getUsers');
        $user_group->post(
            '/new', Handler\User\RegisterUser::class
        )->setName("setUser");
    });
    /**
     * All functions regarding the registration and retrieval of petrinets
     */
    $api_group->group('/' . $_ENV['PETRINET_GROUP'], function($petri_group) {
        $petri_group->get(
            '/{petrinet_id:[0-9]+}', Handler\Petrinet\GetPetrinet::class
        )->setName("getPetrinet");
        $petri_group->get(
            '/[{limit:[0-9]+}/{page:[0-9]+}]', Handler\Petrinet\GetPetrinets::class
        )->setName("getPetrinets");
        $petri_group->get(
            '/{petrinet_id:[0-9]+}/image', Handler\Petrinet\GetPetrinetImage::class
        )->setName('getPetrinetImage');
        $petri_group->post(
            '/new', Handler\Petrinet\RegisterPetrinet::class
        )->setName("setPetrinet");
        $petri_group->post(
            '/feedback',
            Handler\Feedback\CoverabilityFeedback::class
        )->setName("getFeedback");
    });
    /**
     * All functions regarding session management
     */
    $api_group->group('/' . $_ENV['SESSION_GROUP'], function($session_group) {
        $session_group->post(
            '/current', Handler\Session\GetCurrentSession::class
        )->setName("getCurrentSession");
        $session_group->post(
            '/new', Handler\Session\CreateSession::class
        )->setName("newSession");
    });
});

// Catch all non-matching routes and return 404
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
    throw new HttpNotFoundException($request);
});

$app->run();
