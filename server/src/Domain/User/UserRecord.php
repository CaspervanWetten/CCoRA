<?php

namespace Cora\Domain\User;

use DateTime;
use JsonSerializable;

class UserRecord implements JsonSerializable {
    protected $id;
    protected $name;
    protected $created;

    public function __construct(int $id, string $name, DateTime $created) {
        $this->id = $id;
        $this->name = $name;
        $this->created = $created;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getCreated(): DateTime {
        return $this->created;
    }

    public function jsonSerialize(): mixed {
        return [
            "id"         => $this->getId(),
            "name"       => $this->getName(),
            "created_on" => $this->getCreated()
        ];
    }
}
