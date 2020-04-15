<?php

namespace Cora\Views;

interface PetrinetCreatedViewInterface extends ViewInterface {
    public function getId(): int;
    public function setId(int $id): void;
}
