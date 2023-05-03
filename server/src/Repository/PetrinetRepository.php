<?php

namespace Cora\Repository;

use Cora\Domain\Petrinet\PetrinetInterface as IPetrinet;
use Cora\Domain\Petrinet\PetrinetBuilder as PetrinetBuilder;
use Cora\Domain\Petrinet\Place\Place;
use Cora\Domain\Petrinet\Place\PlaceContainer;
use Cora\Domain\Petrinet\Place\PlaceContainerInterface;
use Cora\Domain\Petrinet\Transition\Transition;
use Cora\Domain\Petrinet\Transition\TransitionContainer;
use Cora\Domain\Petrinet\Transition\TransitionContainerInterface;
use Cora\Domain\Petrinet\Flow\Flow;
use Cora\Domain\Petrinet\Flow\FlowMap;
use Cora\Domain\Petrinet\Flow\FlowMapInterface;
use Cora\Domain\Petrinet\Marking\MarkingInterface as IMarking;
use Cora\Domain\Petrinet\Marking\MarkingBuilder;
use Cora\Domain\Petrinet\Marking\Tokens\IntegerTokenCount;
use Cora\Domain\Petrinet\MarkedPetrinetRegisteredResult;

use Exception;
use PDO;

class PetrinetRepository extends AbstractRepository {
    public function getPetrinet($id): ?IPetrinet {
        $this->logger->info("getting petri net", ["id" => $id]);

        if (!$this->petrinetExists($id))
            return NULL;
        $builder = new PetrinetBuilder();
        $builder->addPlaces($this->getPlaces($id));
        $builder->addTransitions($this->getTransitions($id));
        $builder->addFlows($this->getFlows($id));
        $petrinet = $builder->getPetrinet();
        return $petrinet;
    }

    public function getMarking(int $mid, IPetrinet $p): ?IMarking {
        $this->logger->info("getting marking", ["id" => $mid]);

        if (!$this->markingExists($mid)) {
            $this->logger->error("Unable to retrieve marking", ["id" => $mid]);
            return NULL;
        }

        $query = sprintf(
            "SELECT `place`, `tokens` FROM %s WHERE marking = :mid",
            $_ENV['PETRINET_MARKING_PAIR_TABLE']);
        $statement = $this->db->prepare($query);
        $statement->execute([":mid" => $mid]);
        $builder = new MarkingBuilder();
        foreach($statement->fetchAll() as $row) {
            $place = new Place($row["place"]);
            $tokens = new IntegerTokenCount(intval($row["tokens"]));
            $builder->assign($place, $tokens);
        }
        return $builder->getMarking($p);
    }

    public function getMarkings(int $pid) {
        $this->logger->info("getting all markings for petrinet",
                            ["petrinet_id" => $pid]);

        $query = sprintf(
            "SELECT `id` FROM %s WHERE `petrinet` = :pid",
            $_ENV['PETRINET_MARKING_TABLE']);
        $statement = $this->db->prepare($query);
        $statement->execute([":pid" => $pid]);
        return $statement->fetchAll();
    }

    public function getPetrinets(int $limit = 0, int $offset = 0) {
        $this->logger->info("getting petri nets",
                            ["limit" => $limit, "offset" => $offset]);

        $query = sprintf("SELECT `id`, `name` FROM %s",
                         $_ENV['PETRINET_TABLE']);
        if ($limit > 0)
            $query .= sprintf(" LIMIT %d", $limit);
        if ($offset > 0)
            $query .= sprintf(" OFFSET %d", $offset);
        $statement = $this->db->prepare($query);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function saveMarkedPetrinet(
        IPetrinet $pet,
        IMarking $marking,
        $user,
        ?string $name=NULL
    ): MarkedPetrinetRegisteredResult {
        $this->logger->info("saving marked petri net");

        $petrinetId = $this->savePetrinet($pet, $user, $name);
        $markingId = $this->saveMarking($marking, $petrinetId);
        return new MarkedPetrinetRegisteredResult($petrinetId, $markingId);
    }

    public function savePetrinet(
        IPetrinet $petrinet,
        $user,
        ?string $name=NULL
    ): int {
        $this->logger->info("saving petri net", ["name" => $name]);

        $this->db->beginTransaction();
        if (is_null($name))
            $name = sprintf("%s-%s", $user, date("Y-m-d-H:i:s"));
        // insert meta
        $query = sprintf(
            "INSERT INTO %s (`creator`, `name`) VALUES (:creator, :name)",
            $_ENV['PETRINET_TABLE']);
        $statement = $this->db->prepare($query);
        $statement->bindValue(":creator", $user, PDO::PARAM_INT);
        $statement->bindValue(":name", $name, PDO::PARAM_STR);
        $statement->execute();
        $petrinetId = $this->db->lastInsertId();

        try {
            $this->savePlaces($petrinet, $petrinetId);
            $this->saveTransitions($petrinet, $petrinetId);
            $this->saveFlows($petrinet, $petrinetId);
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
        $this->db->commit();
        return $petrinetId;
    }

    public function saveMarking(IMarking $marking, int $pid) {
        $this->logger->info("saving marking", ["petrinet_id" => $pid]);

        // insert meta information
        $query = sprintf("INSERT INTO %s (`petrinet`) VALUES (:pid)",
                         $_ENV['PETRINET_MARKING_TABLE']);
        $statement = $this->db->prepare($query);
        $statement->bindParam(":pid", $pid);
        $statement->execute();
        $markingId = $this->db->lastInsertId();
        // insert place-token pairs
        $values = [];
        foreach($marking->places() as $place)
            array_push($values, sprintf("(:mid, :%sp, :%st)", $place, $place));
        $query = sprintf("INSERT INTO %s (`marking`, `place`, `tokens`) VALUES %s",
                         $_ENV['PETRINET_MARKING_PAIR_TABLE'], implode(", ", $values));
        $statement = $this->db->prepare($query);
        $statement->bindValue(":mid", $markingId);
        foreach($marking as $place => $tokens) {
            $statement->bindValue(sprintf(":%sp", $place), $place, PDO::PARAM_STR);
            $statement->bindValue(sprintf(":%st", $place), $tokens, PDO::PARAM_STR);
        }
        $statement->execute();
        return $markingId;
    }

    public function petrinetExists($id) {
        $query = sprintf("SELECT * FROM %s WHERE `id` = :id",
                         $_ENV['PETRINET_TABLE']);
        $statement = $this->db->prepare($query);
        $statement->execute([":id" => $id]);
        return !empty($statement->fetchAll());
    }

    public function markingExists($id) {
        $query = sprintf("SELECT * FROM %s WHERE `id` = :id",
                         $_ENV['PETRINET_MARKING_TABLE']);
        $statement = $this->db->prepare($query);
        $statement->execute([":id" => $id]);
        return !empty($statement->fetchAll());
    }

    protected function getPlaces(int $id): PlaceContainerInterface {
        $this->logger->info("getting places", ["petrinet_id" => $id]);

        $places = new PlaceContainer();
        $query = sprintf("SELECT `name` FROM %s WHERE petrinet = :pid",
                         $_ENV['PETRINET_PLACE_TABLE']);
        $statement = $this->db->prepare($query);
        $statement->execute([":pid" => $id]);
        foreach($statement->fetchAll() as $row) {
            $place = new Place($row["name"]);
            $places->add($place);
        }
        return $places;
    }

    protected function getTransitions(int $id): TransitionContainerInterface {
        $this->logger->info("getting transitions", ["petrinet_id" => $id]);

        $transitions = new TransitionContainer();
        $query = sprintf("SELECT `name` FROM %s WHERE petrinet = :pid",
                         $_ENV['PETRINET_TRANSITION_TABLE']);
        $statement = $this->db->prepare($query);
        $statement->execute([":pid" => $id]);
        foreach($statement->fetchAll() as $row) {
            $transition = new Transition($row["name"]);
            $transitions->add($transition);
        }
        return $transitions;
    }

    protected function getFlows(int $id): FlowMapInterface {
        $this->logger->info("getting flows", ["petrinet_id" => $id]);

        $flowMap = new FlowMap();
        $fromCol   = "from_element";
        $toCol     = "to_element";
        $weightCol = "weight";
        // place -> transition
        $query = sprintf("SELECT %s, %s, %s FROM %s WHERE `petrinet` = :pid",
                         $fromCol, $toCol, $weightCol,
                         $_ENV['PETRINET_FLOW_PT_TABLE']);
        $statement = $this->db->prepare($query);
        $statement->execute([":pid" => $id]);
        $flows = $statement->fetchAll();
        foreach($flows as $flow) {
            $w = intval($flow[$weightCol]);
            $f = new Flow(new Place($flow[$fromCol]),new Transition($flow[$toCol]));
            $flowMap->add($f, $w);
        }
        // transition -> place
        $query = sprintf("SELECT %s, %s, %s FROM %s WHERE `petrinet` = :pid",
                         $fromCol, $toCol, $weightCol,
                         $_ENV['PETRINET_FLOW_TP_TABLE']);
        $statement = $this->db->prepare($query);
        $statement->execute([":pid" => $id]);
        $flows = $statement->fetchAll();
        foreach($flows as $flow) {
            $w = intval($flow[$weightCol]);
            $f = new Flow(new Transition($flow[$fromCol]), new Place($flow[$toCol]));
            $flowMap->add($f, $w);
        }
        return $flowMap;
    }

    protected function savePlaces(IPetrinet $petrinet, int $id) {
        $this->logger->info("saving places", ["petrinet_id" => $id]);

        $places = $petrinet->getPlaces();
        $values = implode(", ", array_map(function($place) {
            return sprintf("(:pid, :%sname)", $place); }, $places->toArray()));
        $query = sprintf("INSERT INTO %s (`petrinet`, `name`) VALUES %s",
                         $_ENV['PETRINET_PLACE_TABLE'], $values);
        $statement = $this->db->prepare($query);
        $statement->bindParam(":pid", $id, PDO::PARAM_INT);
        foreach($places as $place)
            $statement->bindValue(sprintf(":%sname", $place), $place, PDO::PARAM_STR);
        $statement->execute();
    }

    protected function saveTransitions(IPetrinet $petrinet, int $id) {
        $this->logger->info("saving transitions", ["petrinet_id" => $id]);

        $transitions = $petrinet->getTransitions();
        $values = implode(", ", array_map(function($trans) {
            return sprintf("(:pid, :%sname)", $trans); }, $transitions->toArray()));
        $query = sprintf("INSERT INTO %s (`petrinet`, `name`) VALUES %s",
                         $_ENV['PETRINET_TRANSITION_TABLE'], $values);
        $statement = $this->db->prepare($query);
        $statement->bindParam(":pid", $id, PDO::PARAM_INT);
        foreach($transitions as $trans)
            $statement->bindValue(sprintf(":%sname", $trans), $trans, PDO::PARAM_STR);
        $statement->execute();
    }

    protected function saveFlows(IPetrinet $petrinet, int $id) {
        $this->logger->info("saving flows", ["petrinet_id" => $id]);

        $places = $petrinet->getPlaces();
        $transitions = $petrinet->getTransitions();
        $flows = $petrinet->getFlows();

        $ptValues = [];
        $tpValues = [];
        foreach($flows->flows() as $i => $flow) {
            $from = $flow->getFrom();
            $to = $flow->getTo();
            $s = sprintf("(:pid, :%dfrom, :%dto, :%dweight)", $i, $i, $i);
            if ($from instanceof Place && $places->contains($from) &&
                $to instanceof Transition && $transitions->contains($to))
                array_push($ptValues, $s);
            elseif ($to instanceof Place && $places->contains($to) &&
                    $from instanceof Transition && $transitions->contains($from))
                array_push($tpValues, $s);
            else
                throw new Exception(sprintf("Inconsistent Flow: %s -> %s", $from, $to));
        }
        $ptValues = implode(", ", $ptValues);
        $tpValues = implode(", ", $tpValues);
        $queryFormat = "INSERT INTO %s (`petrinet`, `from_element`,"
                     . "`to_element`, `weight`) VALUES %s";
        $ptQuery = sprintf($queryFormat, $_ENV['PETRINET_FLOW_PT_TABLE'], $ptValues);
        $tpQuery = sprintf($queryFormat, $_ENV['PETRINET_FLOW_TP_TABLE'], $tpValues);
        $ptStatement = $this->db->prepare($ptQuery);
        $tpStatement = $this->db->prepare($tpQuery);
        $ptStatement->bindParam(":pid", $id, PDO::PARAM_INT);
        $tpStatement->bindParam(":pid", $id, PDO::PARAM_INT);
        $i = 0;
        foreach($flows as $flow => $weight) {
            if ($flow->getFrom() instanceof Place &&
                $places->contains($flow->getFrom())) {
                $ptStatement->bindValue(
                    sprintf(":%dfrom", $i), $flow->getFrom(), PDO::PARAM_STR);
                $ptStatement->bindValue(
                    sprintf(":%dto", $i), $flow->getTo(), PDO::PARAM_STR);
                $ptStatement->bindValue(
                    sprintf(":%dweight", $i), $weight, PDO::PARAM_STR);
            } else {
                $tpStatement->bindValue(
                    sprintf(":%dfrom", $i), $flow->getFrom(), PDO::PARAM_STR);
                $tpStatement->bindValue(
                    sprintf(":%dto", $i), $flow->getTo(), PDO::PARAM_STR);
                $tpStatement->bindValue(
                    sprintf(":%dweight", $i), $weight, PDO::PARAM_STR);
            }
            $i++;
        }
        $ptStatement->execute();
        $tpStatement->execute();
    }
}
