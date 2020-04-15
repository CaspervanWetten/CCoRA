<?php

namespace Cora\Handlers\User;

use Slim\Http\Request;
use Slim\Http\Response;

use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Handlers\AbstractHandler;
use Cora\Utils\Paginator;

class GetUsers extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $limit = isset($args["limit"]) ?
               min(MAX_USER_RESULT_SIZE,
                   filter_var($args["limit"], FILTER_SANITIZE_NUMBER_INT)) :
               MAX_USER_RESULT_SIZE;
        $page = isset($args["page"]) ?
              filter_var($args["page"], FILTER_SANITIZE_NUMBER_INT) :
              1;
        $paginator = new Paginator($limit, $page);
        $repo = $this->container->get(UserRepo::class);
        $users = $repo->getUsers(NULL, $paginator->limit(), $paginator->offset());

        $router = $this->container->get("router");
        $nextPage = $paginator->next()->page();
        $prevPage = $paginator->prev()->page();
        return $response->withJson([
            "users" => $users,
            "next_page" => $router->pathFor("getUsers", [
                "limit" => $limit, "page" => $nextPage]),
            "prev_page" => $router->pathFor("getUsers", [
                "limit" => $limit, "page" => $prevPage])
        ]);
    }
}
