<?php

namespace Cora\Domain\Petrinet\View\Json;

use Cora\Domain\Petrinet\MarkedPetrinetRegisteredResult as Result;
use Cora\Domain\Petrinet\View\PetrinetCreatedViewInterface;

class JsonPetrinetCreatedView implements PetrinetCreatedViewInterface {
    protected $result;

    public function setResult(Result $result) {
        $this->result = $result;
    }

    public function getResult(): Result {
        return $this->result;
    }

    public function render(): string {
        $result = $this->getResult();
        return json_encode([
            "petrinet_id" => $result->getPetrinetId(),
            "marking_id" => $result->getMarkingId()
        ]);
    }
}
