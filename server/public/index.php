<?php
defined("CONFIG_FOLDER") or exit("No direct script access allowed.");

use Slim\Factory\AppFactory;
use Slim\Exception\HttpNotFoundException;

use DI\Container;

use Tuupola\Middleware\CorsMiddleware;

use Cora\Repository;
use Cora\Handler;
use Cora\Utils;

/** load the classes via composer **/
require_once VENDOR_FOLDER . DIRECTORY_SEPARATOR . 'autoload.php';

/**************************************
*               SLIM SETUP            *
**************************************/

$container = new Container();
AppFactory::setContainer($container);

$app = AppFactory::create();

/**
 * Pimple Dependency Inection Container (DIC). Holds all objects which
 * may be used by controllers, models and so forth.
 */
$container = $app->getContainer();

$container->set('config', $config);

# Register database connection
$container->set('db', function($c) {
    $settings = $c->get('config');
    $pdo = Utils\DatabaseUtils::connect($settings['db']);
    return $pdo;
});

// Register the repositories
$container->set(Repository\UserRepository::class, function($c) {
    return new Repository\UserRepository($c->get('db'));
});

$container->set(Repository\PetrinetRepository::class, function($c) {
    return new Repository\PetrinetRepository($c->get('db'));
});

$container->set(Repository\SessionRepository::class, function($c) {
    return new Repository\SessionRepository($c->get('db'));
});

/**************************************
*               MIDDLEWARE            *
**************************************/

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->add(new CorsMiddleware([
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
$app->group('/' . API_GROUP, function($api_group) {
    /**
     * All functions regarding the creation and retrieval of users
     */
    $api_group->group('/' . USER_GROUP, function($user_group) {
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
    $api_group->group('/' . PETRINET_GROUP, function($petri_group) {
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
    $api_group->group('/' . SESSION_GROUP, function($session_group) {
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
