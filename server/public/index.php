<?php
defined("CONFIG_FOLDER") or exit("No direct script access allowed.");

/** Slim classes **/
use Slim\App;

use Cora\MiddleWare;
use Cora\Handlers;
use Cora\Utils;

/** load the classes via composer **/
require_once VENDOR_FOLDER . DIRECTORY_SEPARATOR . 'autoload.php';

/**************************************
*               SLIM SETUP            *
**************************************/
$app = new App([
    "settings" => $config,
]);

/**
 * Pimple Dependency Inection Container (DIC). Holds all objects which
 * may be used by controllers, models and so forth.
 */
$container = $app->getContainer();

// Register database connection
$container['db'] = function($c) {
    $pdo = Utils\DatabaseUtils::connect($c['settings']['db']);
    return $pdo;
};

// Register error handlers
$container["notFoundHandler"] = function($c) {
    return new Handlers\Error\NotFoundHandler($c);
};

$container["errorHandler"] = function($c) {
    return new Handlers\Error\ErrorHandler($c);
};

// Register the Repositories
$container[Cora\Domain\User\UserRepository::class] = function($c) {
    return new Cora\Domain\User\UserRepository($c->get('db'));
};

$container[Cora\Domain\Petrinet\PetrinetRepository::class] = function($c) {
    return new Cora\Domain\Petrinet\PetrinetRepository($c->get('db'));
};

$container[Cora\Domain\Session\SessionRepository::class] = function($c) {
    return new Cora\Domain\Session\SessionRepository($c->get('db'));
};

// register the Services
$container[Cora\Services\GetUserService::class] = function($c) {
    return new Cora\Services\GetUserService();
};

$container[Cora\Services\GetUsersService::class] = function($c) {
    return new Cora\Services\GetUsersService();
};

$container[Cora\Services\RegisterUserService::class] = function($c) {
    return new Cora\Services\RegisterUserService();
};

$container[Cora\Services\GetPetrinetService::class] = function($c) {
    return new Cora\Services\GetPetrinetService();
};

$container[Cora\Services\GetPetrinetImageService::class] = function($c) {
    return new Cora\Services\GetPetrinetImageService();
};

$container[Cora\Services\GetPetrinetsService::class] = function($c) {
    return new Cora\Services\GetPetrinetsService();
};

$container[Cora\Services\RegisterPetrinetService::class] = function($c) {
    return new Cora\Services\RegisterPetrinetService();
};

$container[Cora\Services\GetSessionService::class] = function($c) {
    return new Cora\Services\GetSessionService();
};

$container[Cora\Services\StartSessionService::class] = function($c) {
    return new Cora\Services\StartSessionService();
};

$container[Cora\Services\GetFeedbackService::class] = function($c) {
    return new Cora\Services\GetFeedbackService();
};

/**************************************
*               MIDDLEWARE            *
**************************************/

/**
 * This MiddleWare removes trailing slashes from the request url. This
 * does have effect on the registered routes, as they should also be
 * defined without trailing slashes.
 */
$app->add(
    new MiddleWare\TrailingSlash(MiddleWare\TrailingSlash::REMOVE_TRAILING_SLASH)
);

/**
 * This MiddleWare enables Cross Origin Resource Sharing (CORS)
 */
if (CORS_ENABLED) {
    $app->add(
        new MiddleWare\CORS(CORS_ALLOW)
    );
}

/**************************************
*                ROUTES               *
**************************************/

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
            '/{petrinet_id:[0-9]+}', Handlers\Petrinet\GetPetrinet::class
        )->setName("getPetrinet");
        $this->get(
            '[/{limit:[0-9]+}/{page:[0-9]+}]', Handlers\Petrinet\GetPetrinets::class
        )->setName("getPetrinets");
        $this->get(
            '/{petrinet_id:[0-9]+}/image', Handlers\Petrinet\GetPetrinetImage::class
        )->setName('getPetrinetImage');
        $this->post(
            '/new', Handlers\Petrinet\RegisterPetrinet::class
        )->setName("setPetrinet");
        $this->post(
            '/feedback',
            Handlers\Feedback\CoverabilityFeedback::class
        )->setName("getFeedback");
    });
    /**
     * All functions regarding session management
     */
    $this->group('/' . SESSION_GROUP, function() {
        $this->get(
            '/current', Handlers\Session\GetCurrentSession::class
        )->setName("getCurrentSession");
        $this->post(
            '/new', Handlers\Session\CreateSession::class
        )->setName("newSession");
    });
});

$app->run();
