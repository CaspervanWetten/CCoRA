<?php

namespace Cora\Services;

use Cora\Repositories\PetrinetRepository as PetriRepo;
use Cora\Utils\Paginator;

class GetPetrinetsService {
    public function get($page, $limit, PetriRepo $repo) {
        $limit = min(MAX_PETRINET_RESULT_SIZE,
                     filter_var($limit, FILTER_SANITIZE_NUMBER_INT));
        $page = max(1, filter_var($page, FILTER_SANITIZE_NUMBER_INT));
        $paginator = new Paginator($limit, $page);
        $petrinets = $repo->getPetrinets($paginator->limit(), $paginator->offset());
        return $petrinets;
    }
}
