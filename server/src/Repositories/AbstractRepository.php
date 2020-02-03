<?php

namespace Cora\Repositories;

use PDO;

abstract class AbstractRepository {
    protected $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }
}
