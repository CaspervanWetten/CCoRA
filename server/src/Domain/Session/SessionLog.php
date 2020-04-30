<?php

namespace Cora\Domain\Session;

use Cora\Domain\Graphs\GraphInterface as IGraph;

use JsonSerializable;

class SessionLog implements JsonSerializable {
    protected $sessionId;
    protected $userId;
    protected $petrinetId;
    protected $startTime;
    protected $graphs;

    public function __construct(
        int $session,
        int $user,
        int $petrinet,
        int $start = NULL,
        array $graphs = NULL
    ) {
        $this->sessionId = $session;
        $this->userId = $user;
        $this->petrinetId = $petrinet;
        if (is_null($start))
            $this->startTime = $this->timeStamp();
        else
            $this->startTime = $start;
        if (is_null($graphs))
            $this->graphs = [];
        else
            $this->graphs = $graphs;
    }

    public function addGraph(IGraph $graph): void {
        array_push($this->graphs, $graph);
    }

    public function getSessionId(): int {
        return $this->sessionId;
    }

    public function getUserId(): int {
        return $this->userId;
    }

    public function getPetrinetId(): int {
        return $this->petrinetId;
    }

    public function getStartTime(): int {
        return $this->startTime;
    }

    public function getGraphs(): array {
        return $this->graphs;
    }

    public function jsonSerialize() {
        return [
            "user_id"       => $this->getUserId(),
            "session_id"    => $this->getSessionId(),
            "petrinet_id"   => $this->getPetrinetId(),
            "session_start" => date(DATE_ATOM, $this->getStartTime()),
            "graphs"        => $this->getGraphs()
        ];
    }

    protected function timeStamp(): int {
        return time();
    }
}
