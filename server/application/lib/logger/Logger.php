<?php

namespace Cora;

use \Cora\Systems as Systems;
use \Cora\Converters as Converters;

class Logger
{
    /**
     * Create a log file for a user with a certain id
     *
     * @param int $id
     * @return bool returns false on error, true otherwise
     */
    public static function createLog($id)
    {
        $logPath = Logger::getLogPath($id);
        if(!file_exists($logPath) || empty(trim(file_get_contents($logPath)))) {
            // @ suppresses the warning if fopen goes wrong
            $handler = @fopen($logPath, "w");
            if($handler == FALSE) {
                return false;
            }
            $e = [
                "user_id"           => $id,
                "log_created_on"    => Logger::timeStamp(),
                "session_counter"   => 0,
                "sessions"          => [],
            ];
            fwrite($handler, json_encode($e, JSON_PRETTY_PRINT));
            fclose($handler);
        }
        return true;
    }

    /**
     * Gets the current session the user is working on.
     * If no session yet exists it is created.
     *
     * @param int $id   The user id
     * @return int      The current session id
     */
    public static function getCurrentSession($id)
    {
        $log = Logger::getLog($id);
        if($log == FALSE) {
            Logger::createLog($id);
            $log = Logger::getLog($id);
        }
        $log = json_decode($log, true);
        $sess = $log["session_counter"] - 1;
        return $sess;
    }
    
    /**
     * Starts a new session and returns the identifier
     * for this session.
     *
     * @param int $id   The user id
     * @param int $pid  The Petri net id
     * @return int      The new session id
     */
    public static function startNewSession($id, $pid)
    {
        $res = Logger::getCurrentSession($id);
        $log = Logger::getLog($id);
        $newSession = Logger::createSession($pid);
        $contents = json_decode($log, true);        
        $sessions = $contents["sessions"];
        array_push($sessions, $newSession);
        $session_counter = count($sessions);
        $contents["sessions"] = $sessions;
        $contents["session_counter"] = $session_counter;

        Logger::writeLog($id, $contents);
        return $res  + 1;
    }
    
    public static function appendGraph($userId, $graph, $session, $pid = 0)
    {
        $log = Logger::getLog($userId);
        if($log == FALSE) {
            Logger::createLog($userId);
            $log = Logger::getLog($userId);
            $session = Logger::getCurrentSession($userId);
        }
        $contents = json_decode($log, true);
        $sessions = $contents["sessions"];
        if(empty($sessions[$session])) {
            $newSession = Logger::createSession($pid);
            $sessions[$session] = $newSession;
        }
        $attempts = $sessions[$session]["attempts"];
        array_push($attempts, [ 
            "graph" => $graph,
            "timestamp" => Logger::timeStamp()
            ]);
        $sessions[$session]["attempts"] = $attempts;
        $contents["sessions"] = $sessions;
        Logger::writeLog($userId, $contents);
    }

    /**
     * Get the contents of the log file for a user
     *
     * @param int $id   the user belonging to this log
     * @return string   the log file's contents
     */
    protected static function getLog($id)
    {
        $logPath = Logger::getLogPath($id);
        if(!file_exists($logPath)) {
            return FALSE;
        }
        $contents = file_get_contents($logPath);
        return $contents;
    }

    /**
     * Write data to the log file belonging to some user.
     * Note: this data should be an (associative array), not JSON!
     *
     * @param int $id       The id of the user whose log file to write to
     * @param array $data   The data to write
     * @return void
     */
    protected static function writeLog($id, $data)
    {
        $logPath = Logger::getLogPath($id);
        $handle = fopen($logPath, "w");
        fwrite($handle, json_encode($data));
        fclose($handle);
    }

    /**
     * Create a session array belonging to some Petri net
     *
     * @param int $petrinetId   The Petri net for which to create the new session.
     * @return void
     */
    protected static function createSession($petrinetId)
    {
        $e = [
            "petrinet" => $petrinetId,
            "attempts" => []
        ];
        return $e;
    }

    /**
     * Get the path to the log file of a user
     *
     * @param int $id   The user for which to get the path
     * @return void
     */
    protected static function getLogPath($id)
    {
        $logPath = LOG_FOLDER . DIRECTORY_SEPARATOR . $id . ".json";
        return $logPath;
    }

    /**
     * Generate a timestamp
     *
     * @return string   the current datetime
     */
    protected static function timeStamp()
    {
        return date("Y-m-d H:i:s");
    }
}

?>
