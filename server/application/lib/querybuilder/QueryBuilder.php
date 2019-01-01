<?php

namespace Cora\QueryBuilder;

use \Cora\Utils as Utils;

class QueryBuilder
{
    /**
     * The string which is the result of the builder
     * @var string
     */
    protected $res;
    /**
     * Iniitialize variables
     */
    public function __construct($builder = NULL)
    {
        if(!is_null($builder)) {
            $this->res = $builder->toString();
        } else {
            $this->res = "";
        }
    }

    /**
     * Returns the result of the builder
     * @return string The result.
     */
    public function toString()
    {
        $this->res = trim($this->res);
        $this->res .= ";";
        return $this->res;
    }

    /**
     * Magic method version of the toString method.
     * @return string The result.
     */
    public function __toString()
    {
        return $this->toString();
    }

    /***************************************************************************
    *                                SELECTING                                 *
    ***************************************************************************/

    /**
     * Add a SELECT statement.
     * @param  array $project Specifies which columns you want to see
     * @return void
     */
    public function select(array $project = NULL, string $modifier = NULL, $enclosed = FALSE)
    {
        $projection = $this->project($project);
        $this->append("SELECT");
        if (!is_null($modifier)) {
            if ($enclosed) {
                $projection = '(' . $projection . ')';
            }
            $this->append($modifier);
        }
        $this->append($projection);
    }

    /**
     * Add a FROM statement.
     * @param  string $table The table you want data from. May also be a join.
     * @return [type]        [description]
     */
    public function from(string $table)
    {
        $this->append("FROM $table");
    }

    /**
     * Add a WHERE statement
     * @param  string $key   The key you want to filter on
     * @param  string $value Its desired value
     * @return void
     */
    public function where(string $key, string $value)
    {
        $this->append("WHERE $key = $value");
    }

    /**
     * Add an OR part. Adds "OR $key = $value" if specified.
     * @param  string $key   The key you want to filter on
     * @param  string $value Its desired value
     * @return void
     */
    public function or($key = NULL, $value = NULL)
    {
        $s = "OR";
        if (!is_null($key) && !is_null($value))
        {
            $s .= " $key = $value";
        }
        $this->append($s);
    }

    /**
     * Add an AND part. Adds "AND $key = $value" if specified.
     * @param  string $key   The key you want to filter on.
     * @param  string $value Its desired value
     * @return void
     */
    public function and($key = NULL, $value = NULL)
    {
        $s = "AND";
        if (!is_null($key) && !is_null($value))
        {
            $s .= " $key = $value";
        }
        $this->append($s);
    }

    /**
     * Add a LIMIT part.
     * @param  mixed $amount  The amount to limit by.
     * @return void
     */
    public function limit($amount)
    {
        $this->append("LIMIT $amount");
    }

    /**
     * Add an OFFSET part.
     * @param mixed $amount The amount to offset by.
     */
    public function offset($amount = NULL)
    {
        $this->append("OFFSET $amount");
    }

    /**************************************************************************/
    /*                                INSERTING                               */
    /**************************************************************************/

    /**
     * Add an INSERT statement.
     * @param  string $table   The table to insert in.
     * @param  array  $columns Which columns are to be filled in.
     * @param  array  $values  (optional) Which values correspond to the columns
     * @return void
     */
    public function insert($table, $columns, $values = NULL)
    {
        $this->append("INSERT INTO $table");
        $colString = $this->groupArguments($columns);
        $this->append($colString);
        if(!is_null($values))
        {
            $this->values($values);
        }
    }

    /**
     * Add a VALUES part.
     * @param  array $values The column values
     * @return void
     */
    public function values($values)
    {
        $this->append("VALUES");
        if (Utils\ArrayUtils::getNumDimensions($values) == 1)
            $values = [$values];
        $values = array_map(array($this, "groupArguments"), $values);
        $s = $this->groupArguments($values, FALSE);
        $this->append($s);
    }

    /**************************************************************************/
    /*                                DELETING                                */
    /**************************************************************************/

    public function delete($from, $key, $value)
    {
        $s = sprintf("DELETE FROM %s WHERE %s = %s", $from, $key, $value);
        $this->append($s);
    }

    /**
     * Append extra SQL which the class does not support
     * @param string The SQL to be appended
     */
    public function appendCustom($s)
    {
        $this->append($s);
    }

    /**
     * Appends a string to the builder.
     * @param  string $s The string to be appended
     * @return void
     */
    protected function append(string $s)
    {
        $s = filter_var($s, FILTER_SANITIZE_STRING);
        if (!($this->res == "" || $this->res[strlen($this->res) - 1] == " "))
            $this->res .= " ";
        $this->res .= $s;
    }

    /**
     * Groups an array of arguments together and encloses them in parentheses if
     * required. For example:
     * $array = array('first', 'second');
     * groupArguments($array) gives "(first, second)"
     * @param  array   $arguments the arguments to group.
     * @param  boolean $encloded  whether to enclose in parentheses.
     * @return string             the grouped arguments.
     */
    protected function groupArguments(array $arguments, bool $encloded = TRUE)
    {
        $res = "";
        if ($encloded)
            $res .= "(";
        $n = count($arguments);
        for($i = 0; $i < $n - 1; $i++)
        {
            $res .= $arguments[$i] . ", ";
        }
        $res .= $arguments[$n - 1];
        if ($encloded)
            $res .= ")";
        return $res;
    }

    /**
     * Groups the arguments for a projection.
     * @param  array $columns   array of strings of the columns to project.
     * @return string           the grouped strings.
     */
    protected function project(array $columns = NULL)
    {
        $res = "*";
        if (!is_null($columns))
        {
            $res = $this->groupArguments($columns, FALSE);
        }
        return $res;
    }
}

 ?>
