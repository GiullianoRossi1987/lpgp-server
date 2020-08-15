<?php
namespace Adj_Core{
    ///////////////////////////////////////////////////////////////////////////
    // Declaration of classes using and constants
    use Exception;
    use SQLite3;

    if(!defined("URE_PROPVL")) define("URE_PROPVL", 1);
    if(!defined("URE_NRMLVL")) define("URE_NRMLVL", 1);
    if(!defined("DFT_ADJ_DB")) define("DFT_ADJ_DB", $_SERVER['DOCUMENT_ROOT'] . "/core/adj_db.db");
    if(!defined("DEBUG_MODE")) define("DEBUG_MODE", true);

    //////////////////////////////////////////////////////////////////////////
    // Exceptions

    /**
     * <Exception> Thrown when the Adjacent Database Api class try to use a loaded
     * database but there's no database loaded
     */
    class DatabaseNotLoaded extends Exception{
        public function __construct(){ parent::__construct("No Database loaded"); }
    }

    /**
     * <Exception> Thrown when the Adjacent Database Api class try to load a database
     * but there's another database loaded already
     */
    class DatabaseAlreadyLoaded extends Exception{
        public function __construct(){ parent::__construct("There's a database loaded already"); }
    }

    /**
     * <Exception> Thrown when the user reference received don't exist at the main database
     */
    class UserReferenceError extends Exception{

        /**
         * Start the class and throw itself.
         * @param integer $mode The type of user reference (1 -> Proprietary, 2 -> Normal User)
         */
        public function __construct($reference, int $mode = URE_NRMLVL){
            $notation = $mode == 1 ? "Proprietary" : "Normal User";
            parent::__construct("Invalid " . $notation . " user reference " . $reference);
        }
    }

    /**
     * <Exception> Thrown when the reports manager try to add a invalid report
     * subject reference
     */
    class SubjectReferenceError extends Exception{

        public function __construct(int $subject){
            parent::__construct("Invalid subject reference: $subject");
        }
    }

    /**
     * <Exception> Thrown when the connection handler received a SQL error after
     * a query
     */
    class InternalConnectorError extends Exception{
        public function __construct(string $error){
            parent::__construct("INTERNAL CONNECTOR ERROR::SQLITE3 $error");
        }
    }

    /**
     * <Exception> Thrown when the database manager received a invalid database
     */
    class InvalidDatabase extends Exception{
        public function __construct(string $dbpath){
            parent::__construct("INVALID DATABASE::SQLITE3 '$dbpath'");
        }
    }

    ///////////////////////////////////////////////////////////////////////////
    // Main Classes


    /**
     * Manages the conection to the adjacent database of the server. That class
     * connects to a SQLite3 databae (.db file). It manages the database connection
     * and the errors caused at the main connector
     *
     * @var SQLite3|null $connector The main connector of the manager, the resource handler;
     * @var boolean $connected If the manager is connected to a database or not;
     * @var string|null $database The path to the database file connected;
     * @access public
     */
    class SQLite3Connector{
        protected $connector = null;
        protected $connected = false;
        protected $database = null;

        const TMP_TEST_SUBJECT  = ["TMP_TEST", 0];
        const TMP_TEST_REPORT   = ["ONLY_USED_FOR_TESTS", 1, 1, 0];
        const TMP_TEST_FEEDBACK = ["ONLY_USED_FOR_TESTS", 1, 1, 1];

        const ERR_INVALIDATION_DFT = "Invalid, database '%db%'";

        /**
         * Cleans the database after validate the database received.
         * @param SQLite3 $connector The temporary connector handler to access the database
         * @param boolean $rv_success If that parameter is true, then the method
         * will return boolean if the temporary values where removed successfully
         * @return boolean|void;
         */
        private function clsValidationTest(SQLite3 $connector, bool $rv_success = false){
            $rcv = $connector->exec("DELETE FROM tb_sbj_report WHERE nm_subject = \"" . SQLite3Connector::TMP_TEST_SUBJECT[0] . "\";");

            if($rcv === false && $rv_success) return false;
            else if($rcv === false) return;

            // removes the most recent row, and that's the test row, used for the authentication
            $rcv = $connector->exec("DELETE FROM tb_report WHERE cd_report = (SELECT MAX(cd_report) FROM tb_report);");
            if($rcv === false && $rv_success) return false;
            else if($rcv === false) return;

            $rcv = $connector->exec("DELETE FROM tb_feedbacks WHERE cd_feedback = (SELECT MAX(cd_feedback) FROM tb_feedbacks);");

            if($rcv === false && $rv_success) return false;
            else if($rcv === false) return;

            if($rv_success) return true;
        }


        /**
         * Validates the database structure, it inserts a temporary row in each
         * table, if the structure isn't correct, then will return false, otherwise
         * will return true.
         *
         * @param string $path The path to the database file to verify
         * @param boolean $autoDp If the method'll remove the temporary rows
         * after validating
         * @param boolean $verbose If the database isn't valid it will return the SQL error too
         * @return boolean|array
         */
        private function valDatabase(string $path, bool $autoDp = true, bool $verbose = false){
            $connector = new SQLite3($path, SQLITE3_OPEN_READWRITE);
            $tmp_sbj_0 = SQLite3Connector::TMP_TEST_SUBJECT[0];
            $tmp_sbj_1 = SQLite3Connector::TMP_TEST_SUBJECT[1];
            $rc = $connector->exec("INSERT INTO tb_sbj_report (nm_subject, vl_visibility_scope) VALUES (\"$tmp_sbj_0\", $tmp_sbj_1);");
            if($rc === false && $verbose) return [$connector->lastErrorMsg(), false];
            if($rc === false) return false;
            // garbage collecting
            unset($tmp_sbj_0);
            unset($tmp_sbj_1);

            $tmp_rp_0 = SQLite3Connector::TMP_TEST_REPORT[0];
            $tmp_rp_1 = SQLite3Connector::TMP_TEST_REPORT[1];
            $tmp_rp_2 = SQLite3Connector::TMP_TEST_REPORT[2];
            $tmp_rp_3 = SQLite3Connector::TMP_TEST_REPORT[3];

            $rc = $connector->exec("INSERT INTO tb_report (ds_report, id_subject, id_e_user, vl_tp_user) VALUES (\"$tmp_rp_0\", $tmp_rp_1, $tmp_rp_2, $tmp_rp_3);");
            if($rc === false && $verbose) return [$connector->lastErrorMsg(), false];
            if($rc === false) return false;

            // garbage collection
            unset($tmp_rp_0);
            unset($tmp_rp_1);
            unset($tmp_rp_2);
            unset($tmp_rp_3);

            $tmp_fb_0 = SQLite3Connector::TMP_TEST_FEEDBACK[0];
            $tmp_fb_1 = SQLite3Connector::TMP_TEST_FEEDBACK[1];
            $tmp_fb_2 = SQLite3Connector::TMP_TEST_FEEDBACK[2];
            $tmp_fb_3 = SQLite3Connector::TMP_TEST_FEEDBACK[3];

            $rc = $connector->exec("INSERT INTO tb_feedbacks (ds_feedback, tp_feedback, id_e_user, vl_tp_user) VALUES (\"$tmp_fb_0\", $tmp_fb_1, $tmp_fb_2, $tmp_fb_3);");
            if($rc === false && $verbose) return [$connector->lastErrorMsg(), false];
            if($rc === false) return false;

            if($autoDp) $this->clsValidationTest($connector);
            return $verbose ? ["NO ERRORS", true] : true;
        }

        /**
         * Starts a connection with a database after validating it.
         * It only starts the connection if the database is valid.
         *
         * @param string $path The path to the database file
         * @throws DatabaseAlreadyLoaded If there's a database loaded already
         * @throws InvalidDatabase If the database received isn't valid
         * @return void
         */
        public function open(string $path){
            if($this->connected) throw new DatabaseAlreadyLoaded();
            $result = $this->valDatabase($path, true, DEBUG_MODE);
            if((is_array($result) && !$result[1]) || !$result)
                throw new InvalidDatabase(DEBUG_MODE ? $result[0] : $this::ERR_INVALIDATION_DFT);
            unset($result);
            $this->connector = new SQLite3($path);
            $this->connected = true;
            $this->database = $path;
        }

        /**
         * Closes a database connected at the manager
         * @return void;
         */
        public function close(){
            if($this->connected){
                $this->connector->close();
                $this->database = null;
                $this->connected = false;
            }
        }

        /**
         * Checks if the manager have a database connected, it'll check if the
         * SQLite3 manager have a database connected, and if the database attribute
         * isn't null.
         * @return boolean
         */
        public function isConnected(): bool{
            return !is_null($this->database) && $this->connected && !is_null($this->connector);
        }

        /**
         * Starts the class with a database be loaded.
         * @param string|null $path The database path to connect. If it's null, then the class
         *                          will not connect
         * @throws DatabaseAlreadyLoaded If there's a database loaded already
         */
        public function __construct($path = null){
            if(!is_null($path)) $this->open($path);
        }

        /**
         * Destroy the class object at the unset function. It'll close all the
         * loaded databases before removing the object of the memory
         *
         */
        public function __destruct(){
            if($this->connected) $this->close();
        }
    }


}
 ?>
