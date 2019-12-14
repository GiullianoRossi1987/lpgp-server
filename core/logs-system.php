<?php
namespace LogsSystem;

require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Exceptions.php";

use LogsErrors\InvalidFile;
use LogsErrors\LogsFileNotLoaded;
use LogsErrors\LogsFileAlreadyLoaded;

define("DEFAULT_DB_LOGS", $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/logs/database.log");
define("DEFAULT_ERROR_LOGS", $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/logs/error.log");
define("DEFAULT_FILES_LOG", $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/logs/files.log");

/**
 * That class is a handler for the logs management at the system. 
 * That can be used for develop other subclasses.
 * 
 * @var string|null $logs_file The logs file connected
 * @var bool $got_lfile If the class haves a logs file connected.
 * @var array|null $added_actions The actions added and not commited at the class
 * @var DATE_FORMAT The datetime format to use at the files. By default it's '[Y-M-d H:m:i]'
 * @var ALLOWED_FORMATS The file extensions allowadeds to the logs files.
 */

class Logger{
    protected $logs_file;
    protected $got_lfile;
    protected $added_actions;

    const DATE_FORMAT = "[Y-j-d H:m:i]";
    const ALLOWED_FORMATS = ["log"];

    /**
     * Checks if the selected file extension is valid. To be valid it needs to be in the constant ALLOWED_FORMATS
     *
     * @param string $file The file name/path to check.
     * @return bool
     */
    final private function checkFileExt(string $file){
        $sep = explode(".", $file);
        return in_array($sep[count($sep) - 1], self::ALLOWED_FORMATS);
    }

    /**
     * Opens a logs file. That method is the default at the subclasses
     *
     * @param string $lfile The logs file to connect
     * @throws LogsFileAlreadyLoaded If the class have a logs file open already. It's checked by the attributte $got_lfile
     * @throws InvalidFile If the file extension is not valid!
     * @return void
     */
    final public function openLogs(string $lfile){
        if($this->got_lfile) throw new LogsFileAlreadyLoaded("There's a logs file already loaded!", 1);
        if(!$this->checkFileExt($lfile)) throw new InvalidFile("The file '$lfile' is not a valid logs file", 1);
        $this->logs_file = $lfile;
        $this->got_lfile = true;
        $this->added_actions = null;
    }

    /**
     * Closes a logs file.
     *
     * @throws LogsFileNotLoaded If there's no logs file open.
     * @return void
     */
    final public function close(){
        if(!$this->got_lfile) throw new LogsFileNotLoaded("There's no logs file open already!", 1);
        $this->logs_file = null;
        $this->got_lfile = false;
        $this->added_actions = null;
    }

    /**
     * Starts the class with a new connection.
     * 
     * @param string|null $lfile The logs file to open, if it's null then there's no connection, and it'll be a empty constructor.
     * @return void;
     */
    final public function __construct($lfile){
        if(is_null($lfile)){
            $this->logs_file = null;
            $this->got_lfile = false;
        }
        else $this->openLogs($lfile);
    }

    /**
     * Destrois the class and close any open logs file.
     * 
     * @return void
     */
    final public function __destruct(){
        if($this->got_lfile) $this->close();
        return ;
    }

    /**
     * Writes the updates at the logs file.
     * @throws LogsFileNotLoaded If there's no logs file opened at the class
     * @return void;
     */
    final public function commitChanges(){
        if(!$this->got_lfile) throw new LogsFileNotLoaded("There's no logs file loaded!", 1);
        if(is_null($this->added_actions) || count($this->added_actions) <= 0) return ;  // checks if there's new actions if don't just returns void.
        $dumped = implode("\n", $this->added_actions);
        $actual_contents = file_get_contents($this->logs_file);
        file_put_contents($this->logs_file, $actual_contents . "\n" . $dumped);
        $this->added_actions = null;  // clears the added logs
    }

    /**
     * List the logs at the file.
     *
     * @param bool $HTML_format If will return a HTML table to print in the server
     * @return string
     */
    final public function getLogs(bool $HTML_format = true){
        if($HTML_format){
            $content = "<table class=\"default-table\" border=\"2\">\n<thead>\n<tr>\n<th>DateTime</th>\n<th>Action</th>\n<th>Success</th>\n<th>Error Code</th>\n</tr>\n";
            $content .= "<tbody> \n";
            $expl = explode("\n", file_get_contents($this->logs_file));
            foreach($expl as $log){
                $sp = explode("|",$log);
                $log_content = "<tr>\n";
                $log_content .= "<th>" . $sp[0] . "</th>\n";
                $log_content .= "<th>" . $sp[1] . "</th>\n";
                $log_content .= "<th>" . $sp[2] . "</th>\n";
                $log_content .= "<th>" . $sp[3] . "</th>\n";
                $log_content .= "</tr>\n";
                $content .= $log_content;
            }
            $content .= "</table>\n";
            return $content;
        }
        else return file_get_contents($this->logs_file);
    }

    /**
     * That function adds a action on the logs
     *
     * @param string $action the action that the system have done
     * @param bool $success If there was no errors.
     * @param int|null $error_code If there was a error in the execution, the code for it
     * @param bool $auto_commit If the method will commit automaticly.
     * @throws LogsFileNotLoaded If there's no logs file loaded yet.
     * @return void
     */
    final public function addAction(string $action, bool $success = true , $error_code = null, bool $auto_commit = false){
        if(!$this->got_lfile) throw new LogsFileNotLoaded("There's no file loaded!", 1);
        if(is_null($this->added_actions)) $this->added_actions = array();
        $suc = $success ? "1" : "0";
        $err = is_null($error_code) ? "none" : $error_code;
        $dumped_action = date(self::DATE_FORMAT) . "|" . $action . "|" . $suc . "|" . $err;
        array_push($this->added_actions, $dumped_action);
        unset($suc);
        if($auto_commit) $this->commitChanges();
    }

}
?>