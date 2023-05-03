<?php

namespace Cora\Repository;

use Psr\Log\LoggerInterface as Logger;
use PDO;

abstract class AbstractRepository {
    protected $db;
    protected $logger;

    public function __construct(PDO $db, Logger $logger) {
        $this->db = $db;
        $this->logger = $logger;
    }
}
