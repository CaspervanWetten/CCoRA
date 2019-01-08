<?php
defined('APPLICATION_FOLDER') or exit('No direct script access allowed.');

/** Own classes **/
use \Cora\Utils as Utils;
use \Cora\Models as Models;
use \Cora\MiddleWare as MiddleWare;
use \Cora\Controllers as Controllers;
use \Cora\Enumerators\TrailingSlashOptions as TrailingSlashOptions;
use \Cora\ErrorHandlers as ErrorHandlers;
use \Cora\Converters as Converters;

/** Slim classes **/
use \Psr\Http\Message\RequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/** load the classes via composer **/
require_once VENDOR_FOLDER . DIRECTORY_SEPARATOR .  'autoload.php';

/**
 * TODO: Make a route file
 */

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

$container['errorHandler'] = function($c) {
    return new ErrorHandlers\JSONErrorHandler();
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
$app->group('/api', function(){
    /**
     * All functions regarding the creation and retrieval of users
     */
    $this->group('/users', function() {
        // get all users
        $this->get(
            '', Controllers\UserController::class . ':getUsers'
            )->setName('getUsers');
        // get a specific user
        $this->get(
            '/{id:[0-9]+}', Controllers\UserController::class . ':getUser'
            )->setName('getUser');
        // set a new user
        $this->post(
            '/new', Controllers\UserController::class . ':setUser'
            )->setName('setUser');
    });

    /**
     * All functions regarding the registration and retrieval of petrinets
     */
    $this->group('/petrinet', function(){
        $this->get (
            '/{id:[0-9]+}', Controllers\PetrinetController::class . ':getPetrinet'
        )->setName('getPetrinet');
        $this->get (
            '[/{limit:[0-9]+}/{page:[0-9]+}]', Controllers\PetrinetController::class . ':getPetrinets'
        )->setName('getPetrinets');
        $this->get (
            '/{id:[0-9]+}/image', Controllers\PetrinetController::class . ':getImage'
        )->setName('getPetrinetImage');
        $this->post(
            '/{id:[0-9]+}/new', Controllers\PetrinetController::class . ':setPetrinet'
            )->setName('setPetrinet');
        $this->post(
            '/{user_id:[0-9]+}/{petrinet_id:[0-9]+}/{session_id:[0-9]+}/feedback', Controllers\PetrinetController::class . ':getFeedback'
            )->setName('getFeedback');
    });

    /**
     * All functions regarding session management
     */
    $this->group('/session', function() {
        // get the current session for a user
        $this->get(
            '/{id:[0-9]+}/current_session', Controllers\SessionController::class . ':getCurrentSession'
            )->setName('getCurrentSession');
        // start a new session for the user for a Petri net
        $this->post(
            '/{id:[0-9]+}/{pid:[0-9]+}/new_session', Controllers\SessionController::class . ":startNewSession"
            )->setName("startNewSession");
    });
});

$app->run();
?>
