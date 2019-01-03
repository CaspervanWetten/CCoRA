<?php

namespace Cora;

use \Cora\Systems as Systems;
use \Cora\Converters as Converters;
use \Cora\Exceptions\CoraException as CoraException;

class Logger
{
   /**
    * Get the current session id for the user with id $userId
    * @param int $userId the user for which to retrieve the session id
    * @return int the session id for the user
    */
    public static function getCurrentSession($userId) {
        // get the user log file
        $userLog = Logger::getUserLog($userId);
        if($userLog === FALSE) {
            // log file does not exist, create new one
            $userLog = createUserLog($userId);
            if($userLog === FALSE) {
                // could not create a new log file, throw exception
                throw new CoraException("Could not create logging file", 500);
            }
        }
        // current session pointer points at the last element of the abstract
        // session array
        $session = $userLog["session_counter"] - 1;
        return $session;
    }

   /**
    * Start a new session for a user who wants to model a coverability graph
    * for a certain Petri net
    * @param int $userId the user for whom to start the new session
    * @param int $petrinetId the Petri net associated with the new session
    * @return int|bool the id for the new session or FALSE when a new session
    *    could not be started
    */
    public static function startNewSession($userId, $petrinetId) {
        // get the current session id
        $currentSessionId = Logger::getCurrentSession($userId);
        // increment the session id
        $newSessionId     = $currentSessionId + 1;
        // create a new session log file
        if(!Logger::createSessionLog($userId, $newSessionId, $petrinetId)) {
            throw new CoraException("Could not create session log file: name conflict", 500);
        }
        // increment the session counter
        Logger::setSessionCounter($userId, $newSessionId + 1);
        return $newSessionId;
    }

   /**
    * Create and write a new user log file for a specific user
    * @param int $userId the id for the user
    * @return array|bool returns FALSE if the log could not be created
    *    otherwise returns the written data
    */
    public static function createUserLog($userId) {
        $logPath = Logger::getUserLogPath($userId);
        if(file_exists($logPath)) {
            return FALSE;
        }
        $data = Logger::createUserLogData($userId);
        Logger::write($logPath, $data);
        return $data;
    }

    public static function appendGraph($userId, $graph, $session, $pid) {
        $sessionLog = Logger::getSessionLog($userId, $session);
        if(!$sessionLog) {
            throw new CoraException(
                "Could not append graph as session log file does not exist",
                500
            );
        }
        array_push($sessionLog["graphs"], [
            "graph"     => $graph,
            "timestamp" => Logger::timeStamp(),
        ]);
        Logger::write(Logger::getSessionLogPath($userId, $session), $sessionLog);
    }

   /**
    * Create and write a new session log for a specific user and session
    * @param int $userId the user for whom the session is to be created
    * @param int $sessionId the identifier for the new session
    * @param int $petrinetId the Petri net identifier for the new session
    * @return array|bool returns FALSE if the log could not be created
    *    otherwise returns the written data
    */
    protected static function createSessionLog($userId, $sessionId, $petrinetId) {
        $logPath = Logger::getSessionLogPath($userId, $sessionId);
        if(file_exists($logPath)) {
            return FALSE;
        }
        $data = Logger::createSessionLogData($userId, $sessionId, $petrinetId);
        Logger::write($logPath, $data);
        return $data;
    }

   /**
    * Get the log data for a specific user
    * @param int $userId the user id for the user
    * @return array|bool the log data for the user or FALSE if there
    *    is no log for this user
    */
    protected static function getUserLog($userId) {
        $logPath = Logger::getUserLogPath($userId);
        if(!file_exists($logPath)) {
            return FALSE;
        }
        $contents = file_get_contents($logPath);
        return json_decode($contents, TRUE);
    }

   /**
    * Get the session log data for a specific user and session
    * @param int $userId The user associated with the session
    * @param int $sessionId The session identifier
    * @return array|bool the log data regarding the session or FALSE if
    *    there is no log for this session
    */
    protected static function getSessionLog($userId, $sessionId) {
        $logPath = Logger::getSessionLogPath($userId, $sessionId);
        if(!file_exists($logPath)) {
            return FALSE;
        }
        $contents = file_get_contents($logPath);
        return json_decode($contents, TRUE);
    }

   /**
    * Write data (assoc array) to a file in JSON format
    * @param string $path The file path for the file that is to be written
    * @param array $data The data that is to be written to the file
    * @param int $mode The mode for the JSON output (standard JSON_PRETTY_PRINT)
    * @return void
    */
    protected static function write($path, $data, $mode=JSON_PRETTY_PRINT) {
        $handler = fopen($path, "w");
        if($handler === FALSE) {
            return FALSE;
        }
        fwrite($handler, json_encode($data, $mode));
        fclose($handler);
        return TRUE;
    }

   /**
    * Set a new value for the session counter belonging to a particular
    * user.
    * @param int $userId The id for the user
    * @param int $counter The new value for the counter
    * @return void
    */
    protected static function setSessionCounter($userId, $counter) {
        $data = Logger::getUserLog($userId);
        $data["session_counter"] = $counter;
        $logPath = Logger::getUserLogPath($userId);
        if(!file_exists($logPath)) {
            throw new CoraException(
                "Could not set session counter: user log file does not exist"
            );
        }
        Logger::write($logPath, $data);
    }

   /**
    * Create the user data array for the creation of a new log file
    * @param int $userId the id for the user
    * @return array the data array for the user
    */
    protected static function createUserLogData($userId) {
        $data = [
            "user_id"         => $userId,
            "log_created_on"  => Logger::timeStamp(),
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
            "session_id"    => $sessionId,
            "session_start" => Logger::timeStamp(),
            "petrinet_id"   => $petrinetId,
            "graphs"        => []
        ];
        return $data;
    }

   /**
    * Get the path for the user log file for a specific user
    * @param int $userId the id for the user associated with the log file
    * @return string the path for the user log file
    */
    protected static function getUserLogPath($userId) {
        $logPath = LOG_FOLDER . DIRECTORY_SEPARATOR . $userId . ".json";
        return $logPath;
    }

   /**
    * Get the path for the session log file for a specific session
    * @param int $userId the id for the user associated with the session
    * @param int $sessionid the specific session to get
    * @return string the path for the session log file
    */
    protected static function getSessionLogPath($userId, $sessionId) {
        $logPath = LOG_FOLDER . DIRECTORY_SEPARATOR . $userId . "-" . $sessionId . ".json";
        return $logPath;
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
