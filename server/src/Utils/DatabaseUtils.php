<?php

namespace Cora\Utils;

use Exception;

class DatabaseUtils {
    public static function connect(string $dsn, string $user, string $pass) {
        try {
            $db = new \PDO($dsn, $user, $pass);

            // standard attribute settings
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

            return $db;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
