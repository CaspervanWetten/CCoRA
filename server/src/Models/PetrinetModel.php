<?php

namespace Cora\Models;

use \Ds\Map as Map;

use \Cora\Systems\Tokens;
use \Cora\Systems\Petrinet;

use \Cora\QueryBuilder\QueryBuilder as QueryBuilder;

class PetrinetModel extends DatabaseModel {
    /**
     * Get a registered petrinet
     *
     * @param int $id The identifier of the petrinet
     * @return mixed;
     */
    public function getPetrinet($id) {
        // filter input
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        // does the Petri net exist?
        $query = sprintf("SELECT * FROM %s WHERE `id` = :id", PETRINET_TABLE);
        $statement = $this->db->prepare($query);
        $statement->bindValue(":id", $id);
        $this->executeQuery($statement);
        if(empty($statement->fetchAll())) {
            return NULL;
        }
        // collect places
        $query = sprintf(
            "SELECT `name` FROM %s WHERE petrinet = :pid",
            PETRINET_PLACE_TABLE);
        $statement = $this->db->prepare($query);
        $statement->bindValue(":pid", $id);
        $this->executeQuery($statement);
        $places = array_map(
            function($k) { return $k["name"]; }, $statement->fetchAll());
        // collect transitions
        $query = sprintf(
            "SELECT `name` FROM %s WHERE petrinet = :pid",
            PETRINET_TRANSITION_TABLE);
        $statement = $this->db->prepare($query);
        $statement->bindValue(":pid", $id);

        $this->executeQuery($statement);
        $transitions = array_map(
            function($k) { return $k["name"]; }, $statement->fetchAll());
        // collect flows
        $from_col   = "from_element";
        $to_col     = "to_element";
        $weight_col = "weight";
        // place -> transition flows
        $query = sprintf("SELECT %s, %s, %s FROM %s WHERE petrinet = :pid",
                         $from_col, $to_col, $weight_col, PETRINET_FLOW_PT_TABLE);
        $statement = $this->db->prepare($query);
        $statement->bindValue(":pid", $id);
        $this->executeQuery($statement);
        $flows_pt = $statement->fetchAll();
        // transition -> place flows
        $query = sprintf("SELECT %s, %s, %s FROM %s WHERE petrinet = :pid",
                         $from_col, $to_col, $weight_col, PETRINET_FLOW_TP_TABLE);
        $statement = $this->db->prepare($query);
        $statement->bindValue(":pid", $id);
        $this->executeQuery($statement);
        $flows_tp = $statement->fetchAll();

        $flows = array_merge($flows_pt, $flows_tp);
        $flowMap = new Map();
        foreach($flows as $i => $flow) {
            $pair = new Petrinet\Flow($flow[$from_col], $flow[$to_col]);
            $weight = intval($flow[$weight_col]);
            $flowMap->put($pair, $weight);
        }
        // get the initial marking
        // first get the id of the marking
        $query = sprintf(
            "SELECT `id` FROM %s WHERE petrinet = :pid", PETRINET_MARKING_TABLE);
        $statement = $this->db->prepare($query);
        $statement->bindValue(":pid", $id);
        $this->executeQuery($statement);
        // assumption that there is only one marking associated with a Petri net
        // therefore we pick the first marking that is returned
        $rows = $statement->fetchAll();
        $initialMarkingId = NULL;
        if (count($rows) > 0) {
            $initialMarkingId = intval($rows[0]["id"]);
        }
        // get the place-token pairs
        $marking = NULL;
        if (!is_null($initialMarkingId)) {
            $query = sprintf(
                "SELECT `place`, `tokens` FROM %s WHERE marking = :mid",
                PETRINET_MARKING_PAIR_TABLE);
            $statement = $this->db->prepare($query);
            $statement->bindValue(":mid", $initialMarkingId);
            $this->executeQuery($statement);
            $marking = [];
            $markingPairs = $statement->fetchAll();
            foreach($markingPairs as $i => $m) {
                $marking[$m["place"]] = intval($m["tokens"]);
            }
        }
        $petrinet = new Petrinet\Petrinet($places, $transitions, $flowMap, $marking);
        return $petrinet;
    }

   /**
    * Get the meta data for a collection of Petri nets
    * @param int $limit The maximum amount of Petri nets to retrieve
    * @param int $offset The amount to offset the window by
    * @return mixed[] Array of Petri net meta data
    **/
    public function getPetrinets($limit = 0, $offset = 0) {
        $query = sprintf("SELECT `id`, `name` FROM %s", PETRINET_TABLE);
        if($limit > 0) 
            $query .= sprintf(" LIMIT %d", $limit);
        if($offset > 0)
            $query .= sprintf(" OFFSET %d", $offset);
        $statement = $this->db->prepare($query);
        $this->executeQuery($statement);
        return $statement->fetchAll();
    }

   /**
    * Register a Petri net into the database
    * @param Systems\Petrinet $petrinet The Petri net to store
    * @param string $user The name of the Petri net owner
    * @param string $name The name of the Petri net itself
    * @return int The id for the Petri net
    **/
    public function setPetrinet($petrinet, $user, $name=NULL) {
        $this->beginTransaction();
        // set a name for the Petri net if it is not availabe
        if(is_null($name)) {
            $name = sprintf("%s-%s", $user, date("Y-m-d-H:i:s"));
        }
        // register meta information
        $query = sprintf(
            "INSERT INTO %s (`creator`, `name`) VALUES(:creator, :name)",
            PETRINET_TABLE);
        $statement = $this->db->prepare($query);
        $statement->bindValue(":creator", $user, \PDO::PARAM_INT);
        $statement->bindValue(":name", $name, \PDO::PARAM_STR);
        $petrinetId = $this->executeQuery($statement);

        $places      = $petrinet->getPlaces();
        $transitions = $petrinet->getTransitions();
        $flows       = $petrinet->getFlows();

        // register places
        $values = implode(", ", array_map(function($p) {
            return sprintf("(:pid, :%sname)", $p); }, $places->toArray()));
        $query = sprintf(
            "INSERT INTO %s (`petrinet`, `name`) VALUES %s",
            PETRINET_PLACE_TABLE, $values);
        $statement = $this->db->prepare($query);
        $statement->bindValue(":pid", $petrinetId, \PDO::PARAM_INT);
        foreach($places as $i => $place)
            $statement->bindValue(sprintf(":%sname", $place), $place, \PDO::PARAM_STR);
        $this->executeQuery($statement);

        // register transitions
        $values = implode(", ", array_map(function($t) {
            return sprintf("(:pid, :%sname)", $t); }, $transitions->toArray()));
        $query = sprintf(
            "INSERT INTO %s (`petrinet`, `name`) VALUES %s",
            PETRINET_TRANSITION_TABLE, $values);
        $statement = $this->db->prepare($query);
        $statement->bindValue(":pid", $petrinetId, \PDO::PARAM_INT);
        foreach($transitions as $i => $transition) 
            $statement->bindValue(sprintf(":%sname", $transition), $transition, \PDO::PARAM_STR);
        $this->executeQuery($statement);

        // register flows
        $ptValues = [];
        $tpValues = [];
        $i = 0;
        foreach($flows as $flow => $weight) {
            $s = [":pid", sprintf(":%dfrom", $i), sprintf(":%dto", $i), sprintf(":%dweight", $i)];
            $s = sprintf("(:pid, :%dfrom, :%dto, :%dweight)", $i, $i, $i);
            if($places->contains($flow->from) && $transitions->contains($flow->to)) 
                array_push($ptValues, $s);
            elseif($transitions->contains($flow->from) && $places->contains($flow->to)) 
                array_push($tpValues, $s);
            else { // error!
                $this->rollBack();
                throw new \Exception(sprintf(
                    "Inconsistent Flow. From: %s, to: %s",
                    $flow->from,
                    $flow->to)
                );
            }
            $i++;
        }
        $valuesPT = implode(", ", $ptValues);
        $valuesTP = implode(", ", $tpValues);

        $queryPT = sprintf(
            "INSERT INTO %s (`petrinet`, `from_element`, `to_element`, `weight`) "
            . "VALUES %s", PETRINET_FLOW_PT_TABLE, $valuesPT);
        $queryTP = sprintf(
            "INSERT INTO %s (`petrinet`, `from_element`, `to_element`, `weight`) "
            . "VALUES %s", PETRINET_FLOW_TP_TABLE, $valuesTP);

        $statementPT = $this->db->prepare($queryPT);
        $statementTP = $this->db->prepare($queryTP);
        $statementPT->bindValue(":pid", $petrinetId, \PDO::PARAM_INT);
        $statementTP->bindValue(":pid", $petrinetId, \PDO::PARAM_INT);
        $i = 0;
        foreach($flows as $flow => $weight) {
            // place -> transition flow
            if($places->contains($flow->from)) {
                $statementPT->bindValue(sprintf(":%dfrom",   $i), $flow->from  , \PDO::PARAM_STR);
                $statementPT->bindValue(sprintf(":%dto",     $i), $flow->to    , \PDO::PARAM_STR);
                $statementPT->bindValue(sprintf(":%dweight", $i), $weight      , \PDO::PARAM_INT);
            }
            // transition -> place flow
            else {
                $statementTP->bindValue(sprintf(":%dfrom",   $i), $flow->from  , \PDO::PARAM_STR);
                $statementTP->bindValue(sprintf(":%dto",     $i), $flow->to    , \PDO::PARAM_STR);
                $statementTP->bindValue(sprintf(":%dweight", $i), $weight      , \PDO::PARAM_INT);
            }
            $i++;
        }
        $this->executeQuery($statementPT);
        $this->executeQuery($statementTP);

        // register marking meta information
        $query = sprintf("INSERT INTO %s (`petrinet`) VALUES (:pid)",
                         PETRINET_MARKING_TABLE);
        $statement = $this->db->prepare($query);
        $statement->bindValue(":pid", $petrinetId, \PDO::PARAM_INT);
        $markingId = $this->executeQuery($statement);
        $marking = $petrinet->getInitial();

        // register marking map information
        $filteredPlaces = array_filter(
            array_keys($marking->toArray()),
            function($p) use ($marking, $places) {
                if (!$places->contains($p)) {
                    $this->rollBack();
                    throw new \Exception(
                        sprintf("Tokens assigned to place that is not part of the Petri net: %s", $place));
                } 
                $tokens = $marking->get($p);
                if (!$tokens instanceof Tokens\IntegerTokenCount) {
                    $this->rollBack();
                    throw new \Exception(
                        sprintf("Could not store marking: improper type: %s", get_class($tokens)));
                }
                else if ($tokens->value > 0) 
                    return $p;
        });
        $values = implode(", ", array_map(
            function($p) {
                return sprintf("(:mid, :%spid, :%stid)", $p, $p);
            },
            $filteredPlaces
        ));
        $query = sprintf("INSERT INTO %s (`marking`, `place`, `tokens`) VALUES %s",
                         PETRINET_MARKING_PAIR_TABLE, $values);
        $statement = $this->db->prepare($query);
        $statement->bindValue(":mid", $markingId, \PDO::PARAM_INT);
        foreach($marking as $place => $tokens) {
            if($tokens->value <= 0) continue;
            $statement->bindValue(sprintf(":%spid", $place), $place,         \PDO::PARAM_STR);
            $statement->bindValue(sprintf(":%stid", $place), $tokens->value, \PDO::PARAM_INT);
        }
        $this->executeQuery($statement);
        $this->commit();
        return $petrinetId;
    }

    /**
     * Check whether a Petri net exists in the database
     * @param int $id The id for the Petri net
     * @return bool True if it exists, otherwise false
     **/
    public function petrinetExists($id)
    {
        $p = $this->getPetrinet($id);
        return !is_null($p);
    }
}

?>
