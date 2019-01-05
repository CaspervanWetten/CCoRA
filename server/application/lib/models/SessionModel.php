<?php

namespace Cora\Models;

use \Cora\Exceptions\CoraException as CoraException;

class SessionModel
{
   /**
    * Get the current session id for the user with id $userId
    * @param int $userId the user for which to retrieve the session id
    * @return int|bool the session id for the user or FALSE if no session exists
    */
    public function getCurrentSession($userId) {
        $sessionData = $this->getUserLog($userId);
        if(!$sessionData || $sessionData["session_counter"] === 0) {
            return FALSE;
        }
        return $sessionData["session_counter"] - 1;
    }

   /**
    * Start a new session for a user who wants to model a coverability graph
    * for a certain Petri net
    * @param int $userId the user for whom to start the new session
    * @param int $petrinetId the Petri net associated with the new session
    * @return int|bool the id for the new session or FALSE when a new session
    *    could not be started
    */
    public function startNewSession($userId, $petrinetId) {
        $userLog = $this->getUserLog($userId);
        // create user log file if it does not exist yet
        if($userLog === FALSE) {
           $userLog = $this->createUserLog($userId);
           // could not write new user log, stop here
           if($userLog === FALSE) {
               return FALSE;
           }
        }
        $sessionId = $userLog["session_counter"];
        // return FALSE when a session log could not be created
        if($this->createSessionLog($userId, $sessionId, $petrinetId) === FALSE) {
            return FALSE;
        }
        // increment the session counter and update user log file
        $userLog["session_counter"] += 1;
        SessionModel::write(SessionModel::getUserLogPath($userId), $userLog);
        return $sessionId;
    }

   /**
    * Append a graph to a particular session log
    * @param int $userId The user associated with the session
    * @param int $sessionId The session id
    * @param Graph $graph The graph to append to the log
    * @return bool TRUE on succes, FALSE on failure
    */
    public function appendGraph($userId, $sessionId, $graph) {
        $sessionLog = $this->getSessionLog($userId, $sessionId);
        if($sessionLog === FALSE) {
            return FALSE;
        }
        array_push($sessionLog["graphs"], [
            "graph"     => $graph,
            "timestamp" => SessionModel::timeStamp()
            ]);
        return SessionModel::write(
            SessionModel::getSessionLogPath($userId, $sessionId),
            $sessionLog
        );
    }

   /**
    * Get the log data for a specific user
    * @param int $userId the user id for the user
    * @return array|bool the log data for the user or FALSE if there
    *    is no log for this user
    */
    protected function getUserLog($userId) {
        $logPath = SessionModel::getUserLogPath($userId);
        if(!file_exists($logPath)) {
            return FALSE;
        }
        return json_decode(file_get_contents($logPath), TRUE);
    }

   /**
    * Get the session log data for a specific user and session
    * @param int $userId The user associated with the session
    * @param int $sessionId The session identifier
    * @return array|bool the log data regarding the session or FALSE if
    *    there is no log for this session
    */
    protected function getSessionLog($userId, $sessionId) {
        $logPath = SessionModel::getSessionLogPath($userId, $sessionId);
        if(!file_exists($logPath)) {
            return FALSE;
        }
        return json_decode(file_get_contents($logPath), TRUE);
    }

   /**
    * Create and write a new user log file for a specific user
    * @param int $userId the id for the user
    * @return array|bool FALSE on failure, data on succes
    */
    protected function createUserLog($userId) {
        $path = SessionModel::getUserLogPath($userId);
        if(file_exists($path)) {
            return FALSE;
        }
        $data = $this->createUserLogData($userId);
        if(SessionModel::write($path, $data) === FALSE) {
            return FALSE;
        } else {
            return $data;
        }
    }

   /**
    * Create and write a new session log for a specific user and session
    * @param int $userId the user for whom the session is to be created
    * @param int $sessionId the identifier for the new session
    * @param int $petrinetId the Petri net identifier for the new session
    * @return bool FALSE on failure, data on succes
    */
    protected function createSessionLog($userId, $sessionId, $petrinetId) {
        $path = SessionModel::getSessionLogPath($userId, $sessionId);
        if(file_exists($path)) {
            return FALSE;
        }
        $data = $this->createSessionLogData($userId, $sessionId, $petrinetId);
        if(SessionModel::write($path, $data) === FALSE) {
            return FALSE;
        } else {
            return $data;
        }
    }

   /**
    * Create the user data array for the creation of a new log file
    * @param int $userId the id for the user
    * @return array the data array for the user
    */
    protected static function createUserLogData($userId) {
        $data = [
            "user_id"         => $userId,
            "log_created_on"  => SessionModel::timeStamp(),
            "session_counter" => 0,
        ];
        return $data;
    }

   /**
    * Create the session data array for the creation of a new log file
    * @param int $userId the id for the user corresponding to the new session
    * @param int $sessionId the id for the new session that is to be created
    * @param int $petrinetId the id for the Petri net for which
    *    the session is started
    * @return array the data array for the session
    */
    protected static function createSessionLogData($userId, $sessionId, $petrinetId) {
        $data = [
            "user_id"       => $userId,
            "session_id"    => $sessionId,
            "petrinet_id"   => $petrinetId,
            "session_start" => SessionModel::timeStamp(),
            "graphs"        => []
        ];
        return $data;
    }

   /**
    * Write data (assoc array) to a file in JSON format
    * @param string $path The file path for the file that is to be written
    * @param array $data The data that is to be written to the file
    * @param int $mode The mode for the JSON output (standard JSON_PRETTY_PRINT)
    * @return void
    */
    protected static function write($path, $data, $mode=JSON_PRETTY_PRINT) {
        $handle = fopen($path, "w");
        if($handle === FALSE) {
            return FALSE;
        }
        fwrite($handle, json_encode($data, $mode));
        fclose($handle);
        return TRUE;
    }

   /**
    * Get the path for the user log file for a specific user
    * @param int $userId the id for the user associated with the log file
    * @return string the path for the user log file
    */
    protected static function getUserLogPath($userId) {
        return LOG_FOLDER . DIRECTORY_SEPARATOR . $userId . ".json";
    }

   /**
    * Get the path for the session log file for a specific session
    * @param int $userId the id for the user associated with the session
    * @param int $sessionid the specific session to get
    * @return string the path for the session log file
    */
    protected static function getSessionLogPath($userId, $sessionId) {
        return LOG_FOLDER . DIRECTORY_SEPARATOR . $userId . "-" . $sessionId . ".json";
    }

   /**
    * Generate a timestamp
    * @return string the timestamp
    */
    protected static function timeStamp() {
        return date("Y-m-d H:i:s");
    }
}

?>
