<?php

/**
 * @author Lucas Steehouwer
 */

namespace Cozp\Models;

use Cozp\QueryBuilder\QueryBuilder as QueryBuilder;

abstract class Model
{
    /**
     * @var $db PDO object
     */
    protected $db;
    /**
     * base constructor for all the models. This is where the database
     * connection is made.
     * @param array[string]string
     */
    public function __construct($pdo)
    {
        $this->db = $pdo;
    }
    /**
     * execute a PDOStatement. Pass by reference
     * @param  PDOStatement $query The prepared PDOStatement to execute
     * @return int                 the id of the last inserted row.
     */
    public function executeQuery(\PDOStatement &$query, array $params = NULL)
    {
        $this->db->beginTransaction();
        $query->execute($params);
        $res = $this->db->lastInsertId();
        $this->db->commit();
        return $res;
    }

    /**
     * Allows different attributes to be set for the PDO object representing
     * the database
     * @param int   $attribute The attribute to be changed
     * @param mixed $value     The new value
     */
    public function setAttribute(int $attribute, mixed $value)
    {
        $this->db->setAttribute($attribute, $value);
    }
}
?>
