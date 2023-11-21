<?php

namespace Cora\Domain\User;

use JsonSerializable;
use DateTime;

class User implements JsonSerializable {
    protected $id;
    protected $name;
    protected $created;

    public function __construct(int $id=NULL, string $name=NULL, DateTime $created=NULL) {
        $dt = is_null($created) ? new DateTime() : $created;
        $this->setId($id);
        $this->setName($name);
        $this->setCreated($dt);
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getCreated() {
        return $this->created;
    }

    public function setCreated(DateTime $dt) {
        $this->created = $dt;
        return $this;
    }

    public function jsonSerialize(): mixed {
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "created" => $this->getCreated()
        ];
    }
}
