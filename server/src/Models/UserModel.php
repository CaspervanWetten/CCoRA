<?php

namespace Cora\Models;

use \Cora\QueryBuilder\QueryBuilder as QueryBuilder;

class UserModel extends DatabaseModel {
    /**
     * Get all users
     * @param  array    $project an array of columns to be projected
     * @param  int      $limit   the limit on the max amount of results
     * @param  int      $offset  the offset
     * @return array             returns the result as an associative array.
     */
    public function getUsers(array $project = NULL, int $limit = NULL, int $offset = NULL) {
        $columns = is_null($project) ? "*" : implode(",", $project);
        $query = sprintf("SELECT %s FROM %s", $columns, USER_TABLE);
        if (!is_null($limit))
            $query .= sprintf(" LIMIT %d", $limit);
        if (!is_null($offset))
            $query .= sprintf(" OFFSET %d", $offset);
        $statement = $this->db->prepare($query);
        $this->executeQuery($statement);
        return $statement->fetchAll();
    }

    /**
     * Get a specific user, identified by its id.
     * @param  int      $id      the id of the user
     * @param  array    $project which columns to project
     * @return array             The user as associative array
     */
    public function getUser($key, $value, $project = NULL) {
        $columns = is_null($project) ? "*" : implode(",", $project);
        $query = sprintf("SELECT DISTINCT %s FROM %s WHERE %s = :value",
                         $columns,
                         USER_TABLE,
                         $key);
        $statement = $this->db->prepare($query);
        $this->executeQuery($statement, [
            'value' => $value
        ]);
        return $statement->fetch();
    }

    /**
     * Register a user.
     * @param   string $name    the name for the user.
     * @return  int             the id of the inserted user.
     */
    public function setUser($name)
    {
        $this->beginTransaction();
        $query = sprintf(
            "INSERT INTO %s (`name`) VALUES (:name)",
            USER_TABLE);
        $statement = $this->db->prepare($query);
        $statement->bindValue(':name', $name, \PDO::PARAM_STR);
        $result = $this->executeQuery($statement);
        $this->commit();
        return $result;
    }

    /**
     * Delete a user
     * @param   int $id     the id of the user
     * @return  void
     */
    public function delUser($id)
    {
        $this->beginTransaction();
        $query = sprintf("DELETE FROM %s WHERE `id` = :id", USER_TABLE);
        $statement = $this->db->prepare($query);
        $this->executeQuery($statement, [
            "id" => $id
        ]);
        $this->commit();
    }

    public function userExists($id)
    {
        $u = $this->getUser("id", $id);
        return !empty($u);
    }
}
?>
