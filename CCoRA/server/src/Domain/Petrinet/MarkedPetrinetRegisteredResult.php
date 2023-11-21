<?php

namespace Cora\Domain\Petrinet;

class MarkedPetrinetRegisteredResult {
    protected $petrinetId;
    protected $markingId;

    public function __construct(int $pid, int $mid) {
        $this->petrinetId = $pid;
        $this->markingId = $mid;
    }

    public function getPetrinetId(): int {
        return $this->petrinetId;
    }

    public function getMarkingId(): int {
        return $this->markingId;
    }
}
