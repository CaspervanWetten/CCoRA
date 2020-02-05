<?php

namespace Cora\Handlers\Petrinet;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cora\Handlers\AbstractHandler;
use Cora\Repositories\PetrinetRepository as PetrinetRepo;
use Cora\Utils\Paginator;

class GetPetrinets extends AbstractHandler {
    public function handle(Request $request, Response $response, $args) {
        $limit = isset($args["limit"]) ?
               min(MAX_PETRINET_RESULT_SIZE,
                   filter_var($args["limit"], FILTER_SANITIZE_NUMBER_INT)) :
               MAX_PETRINET_RESULT_SIZE;
        $page = isset($args["page"]) ?
              filter_var($args["page"], FILTER_SANITIZE_NUMBER_INT) :
              1;
        $paginator = new Paginator($limit, $page);
        $repo = $this->container->get(PetrinetRepo::class);
        $petrinets = $repo->getPetrinets($paginator->limit(), $paginator->offset());
        $router = $this->container->get("router");
        if (is_null($petrinets))
            $petrinets = array();
        foreach($petrinets as $i => $petrinet) {
            $pid = $petrinet["id"];
            $petrinets[$i]["url"] = $router->pathFor("getPetrinet", ["id" => $pid]);
            $petrinets[$i]["image_url"] = $router->pathFor("getPetrinetImage", [
                "id" => $pid]);
        }
        $nextPage = $paginator->next()->page();
        $prevPage = $paginator->prev()->page();
        return $response->withJson([
            "petrinets" => $petrinets,
            "next_page" => $router->pathFor("getPetrinets", [
                "limit" => $limit,
                "page"  => $nextPage]),
            "prev_page" => $router->pathFor("getPetrinets", [
                "limit" => $limit,
                "page"  => $prevPage])
        ]);
    }
}
