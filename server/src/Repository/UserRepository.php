<?php

namespace Cora\Repository;

use Cora\Domain\User\User;
use Cora\Domain\User\UserRecord;

use DateTime;

class UserRepository extends AbstractRepository {
    /**
     * Get a specific user
     * @param string $key what key to look on
     * @param mixed $value the value to look for
     * @param array $project the columns to show
     * @return array the user as an assoc array
     */
    public function getUser(string $key, $value, array $project=NULL) {
        $columns = is_null($project) ? "*" : implode(",", $project);
        $query = sprintf("SELECT DISTINCT %s FROM %s WHERE %s = :value",
                         $columns,
                         $_ENV['USER_TABLE'],
                         $key);
        $statement = $this->db->prepare($query);
        $statement->execute([
            ":value" => $value
        ]);
        $array = $statement->fetch();
        if (!empty($array)) {
            $user = new User();
            return $user->setId(intval($array["id"]))
                        ->setName($array["name"])
                        ->setCreated(new DateTime($array["created_on"]));
        }
        return NULL;
    }

    /**
     * Get a list of users
     * @param $project which columns to show
     * @param $limit maximum amount of results
     * @param $offset projection offset
     * @return array an array of users
     */
    public function getUsers(
        array $project = NULL,
        int $limit = NULL,
        int $offset = NULL
    ) {
        $columns = is_null($project) ? "*" : implode(",", $project);
        $query = sprintf("SELECT %s FROM %s", $columns, $_ENV['USER_TABLE']);
        if (!is_null($limit))
            $query .= sprintf(" LIMIT %d", $limit);
        if (!is_null($offset))
            $query .= sprintf(" OFFSET %d", $offset);
        $statement = $this->db->prepare($query);
        $statement->execute();
        $result = [];
        foreach($statement->fetchAll() as $row)
            array_push($result, new UserRecord(
                intval($row["id"]),
                $row["name"],
                new DateTime($row["created_on"])));
        return $result;
    }

    /**
     * Register a user
     * @param string $name the name for the user
     * @return int the id for the inserted user
     */
    public function saveUser($name) {
        $this->db->beginTransaction();
        $query = sprintf(
            "INSERT INTO %s (`name`) VALUES (:name)",
            $_ENV['USER_TABLE']);
        $statement = $this->db->prepare($query);
        $statement->bindValue(':name', $name, \PDO::PARAM_STR);
        $statement->execute();
        $id = $this->db->lastInsertId();
        $this->db->commit();
        return $id;
    }

    /**
     * Delete a user
     * @param int $id the id of the user
     * @return void
     */
    public function deleteUser($id) {
        $this->db->beginTransaction();
        $query = sprintf("DELETE FROM %s WHERE `id` = :id",
                         $_ENV['USER_TABLE']);
        $statement = $this->db->prepare($query);
        $statement->execute(["id" => $id]);
        $this->commit();
    }

    /**
     * Determine wheter a user exists
     * @param int $id the id of the user
     * @return bool
     */
    public function userExists(string $key, $value) {
        $u = $this->getUser($key, $value);
        return !empty($u);
    }
}
