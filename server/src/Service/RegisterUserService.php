<?php

namespace Cora\Service;

use Cora\Repository\UserRepository;
use Cora\Domain\User\Validation\UniqueUserRule;
use Cora\Validation\MaxLengthRule;
use Cora\Validation\MinLengthRule;
use Cora\Validation\RegexRule;
use Cora\Validation\RuleValidator;

class RegisterUserService {
    protected $repository;

    public function __construct(UserRepository $repo) {
        $this->repository = $repo;
    }

    public function register($name) {
        $validator = $this->getValidator($this->repository);
        if (!$validator->validate($name))
            return new RegistrationResult(false, NULL, $validator->getError());

        $id = $this->repository->saveUser($name);
        return new RegistrationResult(true, $id, NULL);
    }

    protected function getValidator(UserRepository $repo) {
        $minRule = new MinLengthRule(
            4,
            "Your username is too short. A minimum of four characters is required");
        $maxRule = new MaxLengthRule(
            20,
            "Your username is too long. You may use up to twenty characters");
        $regexRule = new RegexRule(
            "/^\w+$/",
            "Your username contains illegal characters");
        $uniquenessRule = new UniqueUserRule($repo);
        $validator = new RuleValidator([
            $minRule, $maxRule, $regexRule, $uniquenessRule
        ]);
        return $validator;
    }
}

class RegistrationResult {
    protected $success;
    protected $userId;
    protected $errorMessage;

    public function __construct($success, $userId, $errorMessage) {
        $this->userId = $userId;
        $this->errorMessage = $errorMessage;
        $this->success = $success;
    }

    public function succeeded() {
        return $this->success;
    }

    public function failed() {
        return !$this->succeeded();
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getErrorMessage() {
        return $this->errorMessage;
    }
}
