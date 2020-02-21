<?php

namespace Cora\User;

use Cora\User\UserRepository as UserRepo;
use Cora\Validation\AbstractRule;

class UniqueUserRule extends AbstractRule {
    protected $repo;
    
    public function __construct(UserRepo $repo) {
        parent::__construct("A user with this username already exists");
        $this->repo = $repo;
    }

    public function validate($value): bool {
        return !$this->repo->userExists("name", $value);
    }
}
