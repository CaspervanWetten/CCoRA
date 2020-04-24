<?php

namespace Cora\Domain\Session;

use Cora\Domain\Systems\Graphs\GraphInterface as IGraph;
use Cora\Repositories\AbstractRepository;

class SessionRepository extends AbstractRepository {
    public function getCurrentSession(int $userId): Session {
        $log = $this->getMetaLog($userId);
        $session = new Session($log->getSessionCount() - 1);
        return $session;
    }

    public function createNewSession(int $userId, int $petrinetId): Session {
        try {
            $metaLog = $this->getMetaLog($userId);
        } catch (NoMetaLogException $e) {
            $metaLog = $this->createMetaLog($userId);
        }
        $sessionId = $metaLog->getSessionCount();
        $sessionLog = $this->createSessionLog($userId, $petrinetId, $sessionId);
        $metaLog->incrementSessionCounter();
        $this->writeMetaLog($metaLog);
        $this->writeSessionLog($sessionLog);
        $session = new Session($sessionId);
        return $session;
    }

    public function appendGraph(
        int $userId,
        int $sessionId,
        int $petrinetId,
        IGraph $graph
    ) {
        $sessionLog = $this->getSessionLog($userId, $sessionId);
        if ($sessionLog->getPetrinetId() !== $petrinetId)
            throw new InvalidSessionException(
                "Could not add graph: the Petri net for this session does " .
                "not correspond to the given Petri net id");
        $sessionLog->addGraph($graph);
        $this->writeSessionLog($sessionLog);
    }

    protected function getMetaLog(int $userId): MetaSessionLog {
        $logPath = $this->getMetaLogPath($userId);
        if (!file_exists($logPath))
            throw new NoMetaLogException(
                "User with id $userId does not have any sessions");
        $array = json_decode(file_get_contents($logPath), TRUE);
        return new MetaSessionLog(
            intval($array["user_id"]),
            intval($array["session_counter"]));
    }

    protected function getSessionLog(int $userId, int $sessionId): SessionLog {
        $logPath = $this->getSessionLogPath($userId, $sessionId);
        if (!file_exists($logPath))
            throw new NoSessionLogException(
                "User $userId does not have a session with id $sessionId");
        $array = json_decode(file_get_contents($logPath), TRUE);
        return new SessionLog(
            intval($array["session_id"]),
            intval($array["user_id"]),
            intval($array["petrinet_id"]),
            strtotime($array["session_start"]),
            $array["graphs"]);
    }

    protected function createMetaLog(int $userId): MetaSessionLog {
        return new MetaSessionLog($userId);
    }
    
    protected function createSessionLog(
        int $userId,
        int $petrinetId,
        int $sessionId
    ): SessionLog {
        return new SessionLog($sessionId, $userId, $petrinetId);
    }

    protected function writeMetaLog(MetaSessionLog $log): void {
        $path = $this->getMetaLogPath($log->getUserId());
        file_put_contents($path, json_encode($log), LOCK_EX);
    }

    protected function writeSessionLog(SessionLog $log): void {
        $path = $this->getSessionLogPath($log->getUserId(), $log->getSessionId());
        file_put_contents($path, json_encode($log), LOCK_EX);
    }

    protected function getMetaLogPath(int $userId) {
        return LOG_FOLDER . DIRECTORY_SEPARATOR . $userId . ".json";
    }

    protected function getSessionLogPath(int $userId, int $sessionId) {
        return LOG_FOLDER . DIRECTORY_SEPARATOR . $userId . "-" . $sessionId . ".json";
    }
}
