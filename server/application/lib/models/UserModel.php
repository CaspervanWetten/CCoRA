<?php

namespace Cora\Models;

use \Cora\QueryBuilder\QueryBuilder as QueryBuilder;

class UserModel extends Model
{
    /**
     * Get all users
     * @param  array    $project an array of columns to be projected
     * @param  int      $limit   the limit on the max amount of results
     * @param  int      $offset  the offset
     * @return array             returns the result as an associative array.
     */
    public function getUsers(array $project = NULL, int $limit = NULL, int $offset = NULL)
    {
        $qb = new QueryBuilder();
        $qb->select($project);
        $qb->from(USER_TABLE);
        if (!is_null($limit))
            $qb->limit($limit);
        if (!is_null($offset))
            $qb->offset($offset);

        $query = $qb->toString();

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
    public function getUser($key, $value, $project = NULL)
    {
        $qb = new QueryBuilder();
        $qb->select($project, 'DISTINCT');
        $qb->from(USER_TABLE);
        $qb->where($key, ':value');

        $statement = $this->db->prepare($qb->toString());
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
        $qb = new QueryBuilder();
        $qb->insert(USER_TABLE, ['name']);
        $qb->values([':name']);

        $query = $qb->toString();
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
        $builder = new QueryBuilder();
        $builder->delete(USER_TABLE, "id", "?");
        $column = "id";
        $statement = $this->db->prepare($builder->toString());
        $statement->bindParam(1, $id, \PDO::PARAM_INT);
        $this->executeQuery($statement);
        $this->commit();
    }

    public function userExists($id)
    {
        $u = $this->getUser("id", $id);
        return !empty($u);
    }
}
?>
