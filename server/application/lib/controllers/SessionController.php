<?php

namespace Cozp\Controllers;

use Cozp\Logger as Logger;
use Cozp\Models as Models;
use \Psr\Http\Message\RequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class SessionController extends Controller
{
        /**
     * Get the session the user is currently working on
     *
     * @param Request $request      The Psr request object
     * @param Response $response    The Psr response object
     * @param array $args           Argument array containing the user id
     * @return Response             The new response
     */
    public function getCurrentSession(Request $request, Response $response, $args)
    {
        $id = filter_var($args["id"], FILTER_SANITIZE_NUMBER_INT);
        $session = Logger::getCurrentSession($id);
        $model = new Models\UserModel($this->container->get("db"));
        $user = $model->getUser('id', $id);
        if (empty($user)) {
            return $this->show404($request, $response);
        }
        if($session < 0) {
            return $this->showErrors($response, "A session for this user has not yet been created", 404);
        }
        return $response->withJson([
            "session_id" => $session,
        ]);
    }

    /**
     * Start a new session for a user for some Petri net
     *
     * @param Request $request      The Psr request object
     * @param Response $response    The Psr response object
     * @param array $args           Argument array containing the user id and Petri net id
     * @return Response             The new response
     */
    public function startNewSession(Request $request, Response $response, $args)
    {
        $id = filter_var($args["id"], FILTER_SANITIZE_NUMBER_INT);
        $pid = filter_var($args["pid"], FILTER_SANITIZE_NUMBER_INT);
        $model = new Models\UserModel($this->container->get("db"));
        if(!$model->userExists($id)) {
            return $this->showErrors($response, "Could not start a session for this user as it does not exist", 404);
        }
        $model = new Models\PetrinetModel($this->container->get("db"));
        if(!$model->petrinetExists($pid)) {
            return $this->showErrors($response, "Could not start a session for this Petri net as it does not exist", 404);
        }
        $session = Logger::startNewSession($id, $pid);
        return $response->withJson([
            "session_id" => $session
        ]);
    }
}

?>