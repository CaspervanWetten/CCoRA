<?php

namespace Cora\Repository;

use Cora\Domain\Session\Session;
use Cora\Domain\Session\SessionLog;
use Cora\Domain\Session\MetaSessionLog;
use Cora\Domain\Graph\GraphInterface as IGraph;

use Cora\Exception\NoSessionException;
use Cora\Exception\NoSessionLogException;
use Cora\Exception\NoMetaLogException;
use Cora\Exception\InvalidSessionException;

class SessionRepository extends AbstractRepository {
    public function getCurrentSession(int $userId): Session {
        $log = $this->getMetaLog($userId);
        $session = new Session($log->getSessionCount() - 1);
        return $session;
    }

    public function createNewSession(
        int $userId,
        int $petrinetId,
        int $markingId
    ): Session {
        try {
            $metaLog = $this->getMetaLog($userId);
        } catch (NoMetaLogException $e) {
            $metaLog = $this->createMetaLog($userId);
        }
        $sessionId = $metaLog->getSessionCount();
        $sessionLog = $this->createSessionLog(
            $userId,
            $petrinetId,
            $markingId,
            $sessionId
        );
        $metaLog->incrementSessionCounter();
        $this->writeMetaLog($metaLog);
        $this->writeSessionLog($sessionLog);
        $session = new Session($sessionId);
        return $session;
    }

    public function appendGraph(
        int $userId,
        int $sessionId,
        IGraph $graph
    ) {
        $sessionLog = $this->getSessionLog($userId, $sessionId);
        $sessionLog->addGraph($graph);
        $this->writeSessionLog($sessionLog);
    }

    public function sessionExists(
        int $userId,
        int $sessionId
    ): bool {
        $path = $this->getSessionLogPath($userId, $sessionId);
        return file_exists($path);
    }

    public function getMetaLog(int $userId): MetaSessionLog {
        $logPath = $this->getMetaLogPath($userId);
        if (!file_exists($logPath))
            throw new NoMetaLogException(
                "User with id $userId does not have any sessions");
        $array = json_decode(file_get_contents($logPath), TRUE);
        return new MetaSessionLog(
            intval($array["user_id"]),
            intval($array["session_counter"]));
    }

    public function getSessionLog(int $userId, int $sessionId): SessionLog {
        $logPath = $this->getSessionLogPath($userId, $sessionId);
        if (!file_exists($logPath))
            throw new NoSessionLogException(
                "User $userId does not have a session with id $sessionId");
        $array = json_decode(file_get_contents($logPath), TRUE);
        return new SessionLog(
            intval($array["session_id"]),
            intval($array["user_id"]),
            intval($array["petrinet_id"]),
            intval($array["marking_id"]),
            strtotime($array["session_start"]),
            $array["graphs"]);
    }

    protected function createMetaLog(int $userId): MetaSessionLog {
        return new MetaSessionLog($userId);
    }

    protected function createSessionLog(
        int $userId,
        int $petrinetId,
        int $markingId,
        int $sessionId
    ): SessionLog {
        return new SessionLog($sessionId, $userId, $petrinetId, $markingId);
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
        return $_ENV['LOG_FOLDER'] . DIRECTORY_SEPARATOR . $userId . ".json";
    }

    protected function getSessionLogPath(int $userId, int $sessionId) {
        return $_ENV['LOG_FOLDER'] . DIRECTORY_SEPARATOR . $userId . "-" . $sessionId . ".json";
    }
}
