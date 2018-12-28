<?php

namespace Cozp\Models;

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
    public function getPetrinet($id)
    {
        $builder = new QueryBuilder();
        $builder->select(["name"]);
        $builder->from(PETRINET_TABLE);
        $builder->where("id", ":id");

        $statement = $this->db->prepare($builder->toString());
        $statement->bindValue(":id", $id);

        $this->executeQuery($statement);

        $rows = $statement->fetchAll();
        if(count($rows) < 1) {
            return NULL;
        }

        $row = $rows[0];

        $res["name"] = $row["name"];

        // get places and transitions
        $builder = new QueryBuilder();
        $builder->select(["name", "id"]);
        $builder->from(PETRINET_ELEMENT_TABLE);
        $builder->where("petrinet", ":petrinetId");
        $builder->and("type", ":type");

        $query = $builder->toString();

        $statement = $this->db->prepare($query);
        $statement->bindValue(":petrinetId", $id);
        $statement->bindValue(":type", "place");

        $this->executeQuery($statement);

        $rows = $statement->fetchAll();
        $places = [];
        foreach($rows as $i => $place) {
            $key = filter_var($place["id"], FILTER_SANITIZE_STRING);
            $value = $place["name"];
            // $places[$place["id"]] = $place["name"];
            $places[$key] = $value;
        }

        $statement = $this->db->prepare($query);
        $statement->bindValue(":petrinetId", $id);
        $statement->bindValue(":type", "transition");

        $this->executeQuery($statement);

        $transitions = [];
        $rows = $statement->fetchAll();
        foreach($rows as $i => $transition) {
            $transitions[$transition["id"]] = $transition["name"];
        }

        // get flows
        $builder = new QueryBuilder();
        $builder->select(["from_element", "to_element", "weight"]);
        $builder->from(PETRINET_FLOW_TABLE);
        $builder->where("petrinet", ":petrinetId");

        $statement = $this->db->prepare($builder->toString());
        $statement->bindValue(":petrinetId", $id);

        $this->executeQuery($statement);

        $rows = $statement->fetchAll();
        $flows = [];
        foreach($rows as $i => $flow)
        {
            $from;
            $to;
            if( array_key_exists($flow["from_element"] , $places) ) {
                $from = $places[$flow["from_element"]];
            } else {
                $from = $transitions[$flow["from_element"]];
            }
            if( array_key_exists($flow["to_element"] , $places) ) {
                $to = $places[$flow["to_element"]];
            } else {
                $to = $transitions[$flow["to_element"]];
            }
            $flows[$i] = new Systems\Petrinet\Flow($from, $to, intval($flow["weight"]));
        }

        // get marking
        $builder = new QueryBuilder();
        $builder->select(["place", "tokens"]);
        $builder->from(PETRINET_MARKING_PAIR_TABLE);
        $builder->where("petrinet", ":petrinetId");

        $statement = $this->db->prepare($builder->toString());
        $statement->bindValue(":petrinetId", $id);

        $this->executeQuery($statement);

        $rows = $statement->fetchAll();
        $marking = [];
        foreach($rows as $i => $pair) {
            $marking[$places[$pair["place"]]] = intval($pair["tokens"]);
        }
        $res = new Systems\Petrinet\Petrinet($places, $transitions, $flows, $marking);
        return $res;
    }

    public function getPetrinets($limit = 0, $page = 0)
    {
        $offset = 0;

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

    public function setPetrinet($petrinet, $user)
    {
        // register meta information
        $builder = new QueryBuilder();
        $builder->insert(PETRINET_TABLE, ['creator', 'name']);
        $builder->values([":creator", ":name"]);

        $query = $builder->toString();

        $statement = $this->db->prepare($query);
        $statement->bindValue(':creator', $user, \PDO::PARAM_STR);
        $statement->bindValue(':name', "test", \PDO::PARAM_STR);
        $petrinetId = $this->executeQuery($statement);

        // register places and transitions
        foreach($petrinet->getPlaces() as $i => $place) {
            $builder = new QueryBuilder();
            $builder->insert(PETRINET_ELEMENT_TABLE, ["petrinet", "name", "type"]);
            $builder->values([":petrinet", ":name", ":type"]);

            $query = $builder->toString();

            $statement = $this->db->prepare($query);
            $statement->bindValue(':petrinet', $petrinetId);
            $statement->bindValue(':name', $place);
            $statement->bindValue(':type', "place");
            $this->executeQuery($statement);
        }

        foreach($petrinet->getTransitions() as $i => $transition) {
            $builder = new QueryBuilder();
            $builder->insert(PETRINET_ELEMENT_TABLE, ["petrinet", "name", "type"]);
            $builder->values([":petrinet", ":name", ":type"]);

            $query = $builder->toString();

            $statement = $this->db->prepare($query);
            $statement->bindValue(':petrinet', $petrinetId);
            $statement->bindValue(':name', $transition);
            $statement->bindValue(':type', "transition");
            $this->executeQuery($statement);
        }

        // register flows
        foreach($petrinet->getFlows() as $i => $flow) {
            // get the id's for the from and to elements
            $builder = new QueryBuilder();
            $builder->select(["id"]);
            $builder->from(PETRINET_ELEMENT_TABLE);
            $builder->where("petrinet", ":petrinetId");
            $builder->and("name", ":from");

            $statement = $this->db->prepare($builder->toString());
            $statement->bindValue(":petrinetId", $petrinetId);
            $statement->bindValue(":from", $flow->from);

            $this->executeQuery($statement);

            $fromId = intval($statement->fetchAll()[0]["id"]);

            $builder = new QueryBuilder();
            $builder->select(["id"]);
            $builder->from(PETRINET_ELEMENT_TABLE);
            $builder->where("petrinet", ":petrinetId");
            $builder->and("name", ":to");

            $statement = $this->db->prepare($builder->toString());
            $statement->bindValue(":petrinetId", $petrinetId);
            $statement->bindValue(":to", $flow->to);

            $this->executeQuery($statement);

            $toId = intval($statement->fetchAll()[0]["id"]);

            // register
            $builder = new QueryBuilder();
            $builder->insert(PETRINET_FLOW_TABLE, ["petrinet", "from_element", "to_element", "weight"]);
            $builder->values([":petrinet" ,":from", ":to", ":weight"]);

            $statement = $this->db->prepare($builder->toString());
            $statement->bindValue(":petrinet", $petrinetId);
            $statement->bindValue(":from", $fromId);
            $statement->bindValue(":to", $toId);
            $statement->bindValue(":weight", $flow->weight);

            $this->executeQuery($statement);
        }

        // register marking
        $marking = $petrinet->getInitialMarking();
        foreach($marking as $place => $tokenCount) {
            if($tokenCount->value <= 0)
                continue;
            // get the place id
            $builder = new QueryBuilder();
            $builder->select(["id"]);
            $builder->from(PETRINET_ELEMENT_TABLE);
            $builder->where("petrinet", ":petrinetId");
            $builder->and("name", ":place");
            $builder->and("type", ":type");

            $statement = $this->db->prepare($builder->toString());
            $statement->bindValue(":petrinetId", $petrinetId);
            $statement->bindValue(":place", $place);
            $statement->bindValue(":type", "place");

            $this->executeQuery($statement);

            $placeId = $statement->fetchAll()[0]["id"];

            // register
            $builder = new QueryBuilder();
            $builder->insert(PETRINET_MARKING_PAIR_TABLE, ["petrinet", "place", "tokens"]);
            $builder->values([":petrinet", ":place", ":tokens"]);

            $statement = $this->db->prepare($builder->toString());
            $statement->bindValue(":petrinet", $petrinetId);
            $statement->bindValue(":place", $placeId);
            $statement->bindValue(":tokens", $tokenCount);

            $this->executeQuery($statement);
        }
        return $petrinetId;
    }

    public function PetrinetExists($id)
    {
        $p = $this->getPetrinet($id);
        return !is_null($p);
    }
}

?>