<?php
defined("CONFIG_FOLDER") or exit("No direct script access allowed.");

/** Own classes **/
use \Cora\Utils as Utils;
use \Cora\Models as Models;
use \Cora\MiddleWare as MiddleWare;
use \Cora\Controllers as Controllers;
use \Cora\Enumerators\TrailingSlashOptions as TrailingSlashOptions;
use \Cora\ErrorHandlers as ErrorHandlers;
use \Cora\Converters as Converters;

use Cora\Handlers;

/** Slim classes **/
use \Psr\Http\Message\RequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/** load the classes via composer **/
require_once VENDOR_FOLDER . DIRECTORY_SEPARATOR .  'autoload.php';

/**************************************
*               SLIM SETUP            *
**************************************/
// init
$app = new \Slim\App([
    "settings" => $config,
]);

/**
 * Pimple Dependency Inection Container (DIC). Holds all objects which may be
 * used by controllers, models and so forth.
 */
$container = $app->getContainer();

// Register database connection
$container['db'] = function($c) {
    $pdo = Utils\DatabaseUtils::connect($c['settings']['db']);
    return $pdo;
};

$container["notFoundHandler"] = function($c) {
    return new ErrorHandlers\NotFoundHandler();
};

$container["errorHandler"] = function($c) {
    return new Cora\Handlers\ErrorHandler($c);
};

// Register the Controllers
$container[Controllers\UserController::class] = function ($c) {
    return Controllers\UserController::getInstance($c);
};

$container[Controllers\StaticController::class] = function($c) {
    return Controllers\StaticController::getInstance($c);
};

$container[Controllers\PetrinetController::class] = function($c) {
    return Controllers\PetrinetController::getInstance($c);
};

$container[Controllers\SessionController::class] = function($c) {
    return Controllers\SessionController::getInstance($c);
};

// register repositories
$container[Cora\Repositories\UserRepository::class] = function($c) {
    return new Cora\Repositories\UserRepository($c->get('db'));
};

$container[Cora\Repositories\PetrinetRepository::class] = function($c) {
    return new Cora\Repositories\PetrinetRepository($c->get('db'));
};

$container[Cora\Repositories\SessionRepository::class] = function($c) {
    return new Cora\Repositories\SessionRepository($c->get('db'));
};

/**************************************
*               MIDDLEWARE            *
**************************************/

/**
 * This MiddleWare removes trailing slashes from the request url. This does
 * have effect on the registered routes, as they should also be defined without
 * trailing slashes.
 */
$app->add(
    new MiddleWare\TrailingSlash( TrailingSlashOptions::REMOVE_TRAILING_SLASH )
);

/**************************************
*                ROUTES               *
**************************************/

/**
 * Set debug group
 */
$app->group('/debug', function(){
    $this->post(
        '/formdata', function(Request $request, Response $response) {
            $body = $request->getParsedBody();
            $response = $response->withJson($body);

            return $response;
        }
    );
    $this->group('/graph', function(){
        $this->post("/cover/{pid:[0-9]+}", function(Request $request, Response $response, $args) {
            $pid  = $args["pid"];
            $model = new Models\PetrinetModel($this->get('db'));
            $petrinet = $model->getPetrinet($pid);
            $graph = $request->getParsedBody();
            $converter = new Converters\JsonToCoverabilityGraph($graph, $petrinet);
            return $response->withJson($converter->convert());
        });
    });
});

/**
 * Setup api group
 */
$app->group('/' . API_GROUP, function() {
    /**
     * All functions regarding the creation and retrieval of users
     */
    $this->group('/' . USER_GROUP, function() {
        // get all users
        $this->get(
            '/{id:[0-9]+}', Handlers\User\GetUser::class
        )->setName('getUser');
        $this->get(
            '/[{limit:[0-9]+}/{page:[0-9]+}]', Handlers\User\GetUsers::class
        )->setName('getUsers');
        $this->post(
            '/new', Handlers\User\RegisterUser::class
        )->setName("setUser");
    });
    /**
     * All functions regarding the registration and retrieval of petrinets
     */
    $this->group('/' . PETRINET_GROUP, function() {
        $this->get(
            '/{id:[0-9]+}', Handlers\Petrinet\GetPetrinet::class
        )->setName("getPetrinet");
        $this->get(
            '[/{limit:[0-9]+}/{page:[0-9]+}]', Handlers\Petrinet\GetPetrinets::class
        )->setName("getPetrinets");
        $this->get(
            '/{id:[0-9]+}/image', Handlers\Petrinet\GetPetrinetImage::class
        )->setName('getPetrinetImage');
        $this->post(
            '/{id:[0-9]+}/new', Handlers\Petrinet\RegisterPetrinet::class
        )->setName("setPetrinet");
        $this->post(
            '/{user_id:[0-9]+}/{petrinet_id:[0-9]+}/{session_id:[0-9]+}/feedback',
            Handlers\Feedback\CoverabilityFeedback::class
        )->setName("getFeedback");
    });
    /**
     * All functions regarding session management
     */
    $this->group('/' . SESSION_GROUP, function() {
        // get the current session for a user
        $this->get(
            '/{id:[0-9]+}/current', Handlers\Session\GetCurrentSession::class
        )->setName("getCurrentSession");
        // start a new session for the user for a Petri net
        $this->post(
            '/{id:[0-9]+}/{pid:[0-9]+}/new', Handlers\Session\CreateSession::class
        )->setName("newSession");
    });
});

$app->run();
