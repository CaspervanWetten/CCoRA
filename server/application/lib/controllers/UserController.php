<?php

namespace Cora\Controllers;

use \Cora\Models as Models;
use \Cora\Utils as Utils;
use \Cora\Validator\Validator as Validator;
use \Cora\Exceptions\CoraException as CoraException;
use \Psr\Http\Message\RequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class UserController extends Controller
{
    /**
     * Get all the users from the database
     * @param  Request  $request  The Psr request object
     * @param  Response $response The Psr response object
     * @return Response           The response with the data, encoded in JSON.
     */
    public function getUsers(Request $request, Response $response)
    {
        // get the User Model and query for all its users.
        $model = new Models\UserModel($this->container->get('db'));
        $users = $model->getUsers();
        // return 404 when no users are found.
        if (empty($users))
            return $this->show404($request, $response);
        return $response->withJson($users);
    }

    /**
     * Get a specific user
     * @param  Request  $request  The Psr request object
     * @param  Response $response The Psr response object
     * @param  array   $args      The array containing the url options
     * @return Response           The response with the data, encoded in JSON.
     */
    public function getUser(Request $request, Response $response, $args)
    {
        $id = filter_var($args["id"], FILTER_SANITIZE_NUMBER_INT);
        // get the user model and retrieve the user by its id.
        $model = new Models\UserModel($this->container->get('db'));
        $user = $model->getUser('id', $id);
        // return 404 when the user is not found.
        if (empty($user))
            return $this->show404($request, $response);
        return $response->withJson($user);
    }

    /**
     * Register a user into the database
     * @param  Request  $request  The Psr request object, containing the JSON about
     * the new user
     * @param  Response $response The Psr response object.
     * @return Response           The new Response object, containing information
     * about the success or failure of the registration.
     */
    public function setUser(Request $request, Response $response)
    {
        // read and parse the JSON input
        $body = $request->getParsedBody();
        $data = array();
        // filter the contents
        $data['name'] = filter_var($body['name'], FILTER_SANITIZE_STRING);

        $model = new Models\UserModel($this->container->get('db'));
        // check whether the user already exists
        $u = $model->getUser('name', $data['name']);
        if ($u) { // result found: user exists
            throw new CoraException("This username is already being used", 409);
        }
        // else; new user
        // validate the user input
        $validator = new Validator($this->getUserRegistrationConfiguration());
        if (!$validator->validate($data['name'])) {
            throw new CoraException($validator->getError(), 400);
        }
        $id = $model->setUser($data['name']);
        // set up variables for the response
        $router = $this->container->get('router');
        $selfUrl = $router->pathFor('getUser', [
            "id" => $id
        ]);

        // // create log file for this user
        // if(!Logger::createUserLog($id)){
        //     // we have to undo the creation of this user if the creation of the logging did not work
        //     $model->delUser($id);
        //     throw new CoraException("Could not write logging file", 500);
        // }
        
        return $response->withJson([
            "id" => $id,
            "selfUrl" => $selfUrl,
        ], 201);
    }

    protected function getUserRegistrationConfiguration()
    {
        $result = array(
            "min_length" => array(
                "argument" => 4,
                "message" => "Your username is too short. A minimum of four characters is required"
            ),
            "max_length" => array(
                "argument" => 20,
                "message" => "Your username is too long. You may use up to twenty characters"
            )
        );
        return $result;
    }
}

 ?>
