<?php

namespace Cora\Repository;

use Cora\Domain\Session\Session;
use Cora\Domain\Session\SessionLog;
use Cora\Domain\Session\MetaSessionLog;
use Cora\Domain\Graph\GraphInterface as IGraph;

use Cora\Exception\NotFoundException;

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
        $this->logger->info("starting new session",
                            ["user_id"     => $userId,
                             "petrinet_id" => $petrinetId,
                             "marking_id"  => $markingId]);

        try {
            $metaLog = $this->getMetaLog($userId);
        } catch (NotFoundException $e) {
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
        $this->logger->info("appending graph", ["user_id" => $userId,
                                                "session_id" => $sessionId]);

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
        $this->logger->info("getting meta log", ["user_id" => $userId]);

        $logPath = $this->getMetaLogPath($userId);
        if (!file_exists($logPath))
            throw new NotFoundException(
                "User with id=$userId does not have any sessions");
        $array = json_decode(file_get_contents($logPath), TRUE);
        return new MetaSessionLog(
            intval($array["user_id"]),
            intval($array["session_counter"]));
    }

    public function getSessionLog(int $userId, int $sessionId): SessionLog {
        $this->logger->info("getting session log",
                            ["user_id" => $userId, "session_id" => $sessionId]);

        $logPath = $this->getSessionLogPath($userId, $sessionId);
        if (!file_exists($logPath)) {
            $message = "User with id=$userId does not have a session "
                     . "with id=$sessionId";
            throw new NotFoundException($message);
        }
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
        $this->logger->info("creating meta log", ["user_id" => $userId]);

        return new MetaSessionLog($userId);
    }

    protected function createSessionLog(
        int $userId,
        int $petrinetId,
        int $markingId,
        int $sessionId
    ): SessionLog {
        $this->logger->info("creating new session log",
                            ["user_id"     => $userId,
                             "petrinet_id" => $petrinetId,
                             "marking_id"  => $markingId,
                             "session_id"  => $sessionId]);

        return new SessionLog($sessionId, $userId, $petrinetId, $markingId);
    }

    protected function writeMetaLog(MetaSessionLog $log): void {
        $path = $this->getMetaLogPath($log->getUserId());
        file_put_contents($path, json_encode($log), LOCK_EX);

        $this->logger->info("wrote meta log", ["user_id", $log->getUserId()]);
    }

    protected function writeSessionLog(SessionLog $log): void {
        $path = $this->getSessionLogPath($log->getUserId(), $log->getSessionId());
        file_put_contents($path, json_encode($log), LOCK_EX);

        $this->logger->info("wrote session log", ["user_id"    => $log->getUserId(),
                                                  "session_id" => $log->getSessionId()]);
    }

    protected function getMetaLogPath(int $userId) {
        return $_ENV['LOG_FOLDER'] . DIRECTORY_SEPARATOR . $userId . ".json";
    }

    protected function getSessionLogPath(int $userId, int $sessionId) {
        return $_ENV['LOG_FOLDER'] . DIRECTORY_SEPARATOR . $userId . "-" . $sessionId . ".json";
    }
}
