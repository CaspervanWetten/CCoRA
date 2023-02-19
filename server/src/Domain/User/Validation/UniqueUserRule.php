<?php

namespace Cora\Domain\User\Validation;

use Cora\Validation\AbstractRule;
use Cora\Repository\UserRepository;

class UniqueUserRule extends AbstractRule {
    protected $repo;

    public function __construct(UserRepository $repo) {
        parent::__construct("A user with this username already exists");
        $this->repo = $repo;
    }

    public function validate($value): bool {
        return !$this->repo->userExists("name", $value);
    }
}
