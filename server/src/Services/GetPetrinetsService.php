<?php

namespace Cora\Services;

use Cora\Repositories\PetrinetRepository as PetriRepo;
use Cora\Utils\Paginator;
use Cora\Views\PetrinetsViewInterface as View;

class GetPetrinetsService {
    public function get(View &$view, $page, $limit, PetriRepo $repo) {
        $limit = min(MAX_PETRINET_RESULT_SIZE,
                     filter_var($limit, FILTER_SANITIZE_NUMBER_INT));
        $page = max(1, filter_var($page, FILTER_SANITIZE_NUMBER_INT));
        $paginator = new Paginator($limit, $page);
        $petrinets = $repo->getPetrinets($paginator->limit(), $paginator->offset());
        $view->setPetrinets($petrinets);
    }
}
