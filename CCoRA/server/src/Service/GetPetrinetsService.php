<?php

namespace Cora\Service;

use Cora\Repository\PetrinetRepository;
use Cora\Utils\Paginator;

class GetPetrinetsService {
    private $repository;

    public function __construct(PetrinetRepository $repository) {
        $this->repository = $repository;
    }

    public function get($page, $limit) {
        $page  ??= 1;
        $limit ??= $_ENV['MAX_PETRINET_RESULT_SIZE'];

        $limit = min($_ENV['MAX_PETRINET_RESULT_SIZE'],
                     filter_var($limit, FILTER_SANITIZE_NUMBER_INT));
        $page = max(1, filter_var($page, FILTER_SANITIZE_NUMBER_INT));
        $paginator = new Paginator($limit, $page);
        return $this->repository->getPetrinets($paginator->limit(), $paginator->offset());
    }
}
