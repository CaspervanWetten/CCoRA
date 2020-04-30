<?php

namespace Cora\Domain\Petrinet\View;

use Cora\Views\ViewInterface;

interface PetrinetCreatedViewInterface extends ViewInterface {
    public function getId(): int;
    public function setId(int $id): void;
}
