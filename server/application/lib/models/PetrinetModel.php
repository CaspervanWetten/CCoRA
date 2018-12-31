<?php

namespace Cozp\Models;

use Ds\Map as Map;
use Cozp\Systems as Systems;
use Cozp\querybuilder\QueryBuilder as QueryBuilder;

class PetrinetModel extends Model
{
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
        $builder = new QueryBuilder();
        $builder->select();
        $builder->from(PETRINET_TABLE);
        $builder->where("id", ":id");
        $statement = $this->db->prepare($builder->toString());
        $statement->bindValue(":id", $id);
        $this->executeQuery($statement);
        if(empty($statement->fetchAll())) {
            return NULL;
        }
        // collect places
        $builder = new QueryBuilder();
        $builder->select(["name"]);
        $builder->from(PETRINET_PLACE_TABLE);
        $builder->where("petrinet", ":pid");

        $statement = $this->db->prepare($builder->toString());
        $statement->bindValue(":pid", $id);

        $this->executeQuery($statement);
        $places = array_map(function($k) { return $k["name"]; }, $statement->fetchAll());

        // collect transitions
        $builder = new QueryBuilder();
        $builder->select(["name"]);
        $builder->from(PETRINET_TRANSITION_TABLE);
        $builder->where("petrinet", ":pid");

        $statement = $this->db->prepare($builder->toString());
        $statement->bindValue(":pid", $id);

        $this->executeQuery($statement);
        $transitions = array_map(function($k) { return $k["name"]; }, $statement->fetchAll());

        // collect flows
        $from_col   = "from_element";
        $to_col     = "to_element";
        $weight_col = "weight";
        // place -> transition flows
        $builder  = new QueryBuilder();
        $builder->select([$from_col, $to_col, $weight_col]);
        $builder->from(PETRINET_FLOW_PT_TABLE);
        $builder->where("petrinet", ":pid");

        $statement = $this->db->prepare($builder->toString());
        $statement->bindValue(":pid", $id);
        $this->executeQuery($statement);
        $flows_pt = $statement->fetchAll();
        // transition -> place flows
        $builder = new QueryBuilder();
        $builder->select([$from_col, $to_col, $weight_col]);
        $builder->from(PETRINET_FLOW_TP_TABLE);
        $builder->where("petrinet", ":pid");

        $statement = $this->db->prepare($builder->toString());
        $statement->bindValue(":pid", $id);
        $this->executeQuery($statement);
        $flows_tp = $statement->fetchAll();

        $flows = array_merge($flows_pt, $flows_tp);
        $flowMap = new Map();
        foreach($flows as $i => $flow) {
            $pair = new Systems\Petrinet\Flow($flow[$from_col], $flow[$to_col]);
            $weight = intval($flow[$weight_col]);
            $flowMap->put($pair, $weight);
        }
        // get the initial marking
        // first get the id of the marking
        $builder = new QueryBuilder();
        $builder->select(["id"]);
        $builder->from(PETRINET_MARKING_TABLE);
        $builder->where("petrinet", ":pid");

        $statement = $this->db->prepare($builder->toString());
        $statement->bindValue(":pid", $id);
        $this->executeQuery($statement);
        // assumption that there is only one marking associated with a Petri net
        // therefore we pick the first marking that is returned
        $initialMarkingId = intval($statement->fetchAll()[0]["id"]);

        // get the place-token pairs
        $builder = new QueryBuilder();
        $builder->select(["place", "tokens"]);
        $builder->from(PETRINET_MARKING_PAIR_TABLE);
        $builder->where("marking", ":mid");
        $statement = $this->db->prepare($builder->toString());
        $statement->bindValue(":mid", $initialMarkingId);

        $this->executeQuery($statement);
        $markingPairs = $statement->fetchAll();
        $marking = [];
        foreach($markingPairs as $i => $m) {
            $marking[$m["place"]] = intval($m["tokens"]);
        }

        $petrinet = new Systems\Petrinet\Petrinet($places, $transitions, $flowMap, $marking);
        return $petrinet;
    }

    public function getPetrinets($limit = 0, $page = 0)
    {
        $builder = new QueryBuilder();
        $builder->select(["id", "name"]);
        $builder->from(PETRINET_TABLE);
        if($limit > 0 && $page >= 0) {
            $offset = $page * $limit;
            $builder->limit($limit);
            $builder->offset($offset);
        } else {
            throw new \Exception('Illegal parameters');
        }
        $statement = $this->db->prepare($builder->toString());
        $this->executeQuery($statement);
        return $statement->fetchAll();
    }

    public function setPetrinet($petrinet, $user, $name=NULL)
    {
        $this->beginTransaction();
        // set a name for the Petri net if it is not availabe
        if(is_null($name)) {
            $name = sprintf("%s-%s", $user, date("Y-m-d-H:i:s"));
        }
        // register meta information
        $builder = new QueryBuilder();
        $builder->insert(PETRINET_TABLE, ['creator', 'name']);
        $builder->values([":creator", ":name"]);
        $statement = $this->db->prepare($builder->toString());
        $statement->bindValue(":creator", $user, \PDO::PARAM_INT);
        $statement->bindValue(":name", $name, \PDO::PARAM_STR);
        $petrinetId = $this->executeQuery($statement);

        $places      = $petrinet->getPlaces();
        $transitions = $petrinet->getTransitions();
        $flows       = $petrinet->getFlows();

        // register places
        $builder = new QueryBuilder();
        $builder->insert(PETRINET_PLACE_TABLE, ["petrinet", "name"]);
        $place_values = [];
        foreach($places as $i => $place) {
            array_push($place_values, [":pid", sprintf(":%sname", $i)]);
        }
        $builder->values($place_values);
        $statement = $this->db->prepare($builder->toString());
        $statement->bindValue(":pid", $petrinetId, \PDO::PARAM_INT);
        foreach($places as $i => $place) {
            $statement->bindValue(sprintf(":%sname", $i), $place, \PDO::PARAM_STR);
        }
        $this->executeQuery($statement);

        // register transitions
        $builder = new QueryBuilder();
        $builder->insert(PETRINET_TRANSITION_TABLE, ["petrinet", "name"]);
        $transition_values = [];
        foreach($transitions as $i => $transition) {
            array_push($transition_values, [":pid", sprintf(":%sname", $i)]);
        }
        $builder->values($transition_values);
        $statement = $this->db->prepare($builder->toString());
        $statement->bindValue(":pid", $petrinetId, \PDO::PARAM_INT);
        foreach($transitions as $i => $transition) {
            $statement->bindValue(sprintf(":%sname", $i), $transition, \PDO::PARAM_STR);
        }
        $this->executeQuery($statement);

        $builderPT = new QueryBuilder();
        $builderTP = new QueryBuilder();

        $builderPT->insert(PETRINET_FLOW_PT_TABLE,
            ["petrinet", "from_element", "to_element", "weight"]);
        $builderTP->insert(PETRINET_FLOW_TP_TABLE,
            ["petrinet", "from_element", "to_element", "weight"]);
        $ptValues = [];
        $tpValues = [];
        $i = 0;
        foreach($flows as $flow => $weight) {
            $s = [":pid", sprintf(":%sfrom", $i), sprintf(":%sto", $i), sprintf(":%sweight", $i)];
            // place -> transition flow
            if($places->contains($flow->from) && $transitions->contains($flow->to)) {
                array_push($ptValues, $s);
            }
            // transition -> place flow
            elseif($transitions->contains($flow->from) && $places->contains($flow->to)) {
                array_push($tpValues, $s);
            }
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
        $builderPT->values($ptValues);
        $builderTP->values($tpValues);
        $statementPT = $this->db->prepare($builderPT->toString());
        $statementTP = $this->db->prepare($builderTP->toString());
        $statementPT->bindValue(":pid", $petrinetId, \PDO::PARAM_INT);
        $statementTP->bindValue(":pid", $petrinetId, \PDO::PARAM_INT);
        $i = 0;
        foreach($flows as $flow => $weight) {
            // place -> transition flow
            if($places->contains($flow->from)) {
                $statementPT->bindValue(sprintf(":%sfrom",   $i), $flow->from  , \PDO::PARAM_STR);
                $statementPT->bindValue(sprintf(":%sto",     $i), $flow->to    , \PDO::PARAM_STR);
                $statementPT->bindValue(sprintf(":%sweight", $i), $weight      , \PDO::PARAM_INT);
            }
            // transition -> place flow
            else {
                $statementTP->bindValue(sprintf(":%sfrom",   $i), $flow->from  , \PDO::PARAM_STR);
                $statementTP->bindValue(sprintf(":%sto",     $i), $flow->to    , \PDO::PARAM_STR);
                $statementTP->bindValue(sprintf(":%sweight", $i), $weight      , \PDO::PARAM_INT);
            }
            $i++;
        }
        $this->executeQuery($statementPT);
        $this->executeQuery($statementTP);

        $builder = new QueryBuilder();
        $builder->insert(PETRINET_MARKING_TABLE, ["petrinet"]);
        $builder->values([":pid"]);
        $statement = $this->db->prepare($builder->toString());
        $statement->bindValue(":pid", $petrinetId, \PDO::PARAM_INT);
        $markingId = $this->executeQuery($statement);

        $marking = $petrinet->getInitialMarking();
        $builder = new QueryBuilder();
        $builder->insert(PETRINET_MARKING_PAIR_TABLE, ["marking", "place", "tokens"]);
        $markingValues = [];
        foreach($marking as $place => $tokens) {
            if($tokens instanceof Systems\IntegerTokenCount &&
               $places->contains($place) && $tokens->value > 0) {
                array_push(
                    $markingValues,
                    [":mid", sprintf(":%spid", $place), sprintf(":%stok", $place)]
                );
            } elseif(!$places->contains($place)) {
                $this->rollBack();
                throw new \Exception(
                    sprintf("Tokens assigned to place that is not part of the Petri net: %s", $place));
            } elseif(!$tokens instanceof Systems\IntegerTokenCount) {
                $this->rollBack();
                throw new \Exception(
                    sprintf("Could not store marking: improper type: %s", get_class($tokens)));
            }
        }
        $builder->values($markingValues);
        $statement = $this->db->prepare($builder->toString());
        $statement->bindValue(":mid", $markingId, \PDO::PARAM_INT);
        foreach($marking as $place => $tokens) {
            if($tokens->value <= 0) continue;
            $statement->bindValue(sprintf(":%spid", $place), $place,         \PDO::PARAM_STR);
            $statement->bindValue(sprintf(":%stok", $place), $tokens->value, \PDO::PARAM_INT);
        }
        $this->executeQuery($statement);
        $this->commit();
        return $petrinetId;
    }

    public function petrinetExists($id)
    {
        $p = $this->getPetrinet($id);
        return !is_null($p);
    }
}

?>
