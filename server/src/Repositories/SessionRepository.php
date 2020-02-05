<?php

namespace Cora\Repositories;

class SessionRepository extends AbstractRepository {
    public function getCurrentSession($userId) {
        $sessionData = $this->getMetaLog($userId);
        if (!$sessionData || $sessionData["session_counter"] === 0)
            return FALSE;
        return $sessionData["session_counter"] - 1;
    }

    public function createNewSession($userId, $petrinetId) {
        $metaLog = $this->getMetaLog($userId);
        if ($metaLog === FALSE) {
            if (file_exists($this->getMetaLogPath($userId))) 
                return FALSE;
            if ($this->createMetaLog($userId) === FALSE)
                return FALSE;
            if (($metaLog = $this->getMetaLog($userId)) === FALSE)
                return FALSE;
        }
        $sessionId = $metaLog["session_counter"];
        if (!$this->createSessionLog($userId, $petrinetId, $sessionId))
            return FALSE;
        $metaLog["session_counter"] += 1;
        $logPath = $this->getMetaLogPath($userId);
        file_put_contents($logPath, json_encode($metaLog), LOCK_EX);
        return $metaLog["session_counter"] - 1;
    }

    public function appendGraph($userId, $sessionId, $graph) {
        $log = $this->getSessionLog($userId, $sessionId);
        array_push($log["graphs"], $graph);
        return file_put_contents(json_encode($log), LOCK_EX);
    }

    protected function getMetaLog($userId) {
        $logPath = $this->getMetaLogPath($userId);
        if (!file_exists($logPath))
            return FALSE;
        return json_decode(file_get_contents($logPath), TRUE);
    }

    protected function getSessionLog($userId, $sessionId) {
        $logPath = $this->getSessionLogPath($userId, $sessionId);
        if (!file_exists($logPath))
            return FALSE;
        return json_decode(file_get_contents($logPath), TRUE);
    }

    protected function createMetaLog($userId) {
        $data = $this->createMetaLogData($userId);
        $path = $this->getMetaLogPath($userId);
        return file_put_contents($path, json_encode($data), LOCK_EX);
    }

    protected function createSessionLog($userId, $petrinetId, $sessionId) {
        $data = $this->createSessionLogData($userId, $petrinetId, $sessionId);
        $path = $this->getSessionLogPath($userId, $sessionId);
        return file_put_contents($path, json_encode($data), LOCK_EX);
    }
    
    protected function createMetaLogData($userId) {
        $data = [
            "user_id" => $userId,
            "created_on" => $this->timestamp(),
            "session_counter" => 0
        ];
        return $data;
    }
    
    protected function createSessionLogData($userId, $petrinetId, $sessionId) {
        $data = [
            "user_id" => $userId,
            "session_id" => $sessionId,
            "petrinet_id" => $petrinetId,
            "session_start" => $this->timestamp(),
            "graphs" => []
        ];
        return $data;
    }

    protected function getMetaLogPath($userId) {
        return LOG_FOLDER . DIRECTORY_SEPARATOR . $userId . ".json";
    }

    protected function getSessionLogPath($userId, $sessionId) {
        return LOG_FOLDER . DIRECTORY_SEPARATOR . $userId . "-" . $sessionId . ".json";
    }

    protected function timestamp() {
        return date("Y-m-d H:i:s");
    }
}
