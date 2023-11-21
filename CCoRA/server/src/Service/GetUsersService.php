<?php

namespace Cora\Service;

use Cora\Repository\UserRepository;
use Cora\Utils\Paginator;

class GetUsersService {
    private $repository;

    public function __construct(UserRepository $repo) {
        $this->repository = $repo;
    }

    public function getUsers($page, $limit) {
        $page  ??= 1;
        $limit ??= $_ENV['MAX_USER_RESULT_SIZE'];

        $limit = min($_ENV['MAX_USER_RESULT_SIZE'],
                     filter_var($limit, FILTER_SANITIZE_NUMBER_INT));
        $page = filter_var($page, FILTER_SANITIZE_NUMBER_INT);
        $paginator = new Paginator($limit, $page);
        return $this->repository->getUsers(NULL, $paginator->limit(),
                                           $paginator->offset());
    }
}
