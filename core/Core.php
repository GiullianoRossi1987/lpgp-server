<?php
namespace Core;

require_once "/var/www/html/lpgp-server/core/Exceptions.php";
require_once "/var/www/html/lpgp-server/core/session.php";
require_once "/var/www/html/lpgp-server/PHPMailer/src/PHPMailer.php";
require_once "/var/www/html/lpgp-server/PHPMailer/src/Exception.php";
// add the logs manager after.

// Session system.
use SessionSystem\SessionSystem;
use PHPMailer\PHPMailer\PHPMailer;
use mysqli;
use mysqli_result;

// Exceptions
use DatabaseActionsExceptions\AlreadyConnectedError;
use DatabaseActionsExceptions\NotConnectedError;

use UsersSystemExceptions\InvalidUserName;
use UsersSystemExceptions\PasswordAuthError;
use UsersSystemExceptions\UserAlreadyExists;
use UsersSystemExceptions\UserNotFound;

use ProprietariesExceptions\AuthenticationError;
use ProprietariesExceptions\InvalidProprietaryName;
use ProprietariesExceptions\ProprietaryNotFound;
use ProprietariesExceptions\ProprietaryAlreadyExists;

use SignaturesExceptions\InvalidSignatureFile;
use SignaturesExceptions\SignatureAuthError;
use SignaturesExceptions\SignatureNotFound;


define("DEFAULT_HOST", "localhost");
define("DEFAULT_DB", "LPGP_WEB");

class DatabaseConnection{
    /**
     * That class contains the main connection to the database and him universal actions,
     * such as connect, disconnect and get connection info.
     * @var mysqli $connection The main connection with the database.
     * @var string $database_connected The database wich is connected.
     * @var string $host_using The host/IP using for the database server connection.
     * @var bool $got_connection If the class is connected to a MySQL database
     * @var string $user_connected The user that's doing the connection.
     * @author Giulliano Ross <giulliano.scatalon.rossi@gmail.com>
     */
    private $connection;
    private $database_connected;
    private $host_using;
    private $got_connection;
    private $user_connected;

    public function checkNotConnected(bool $auto_throw = true){
        /**
         * Checks if the class's connected to a database.
         * @param bool $auto_throw If there's no connection, if the method will throw the error by default.
         * @throws NotConnectedError If there's no connection, and the method is allowed to throw that exception.
         * @return bool|void
         */ 
        if(!$this->got_connection){
            if($auto_throw) throw new NotConnectedError("There's no connection with a MySQL database!", 1);
            else return false;
        }
        else return true;
    }

    public function __construct(string $user, string $passwd, string $host = DEFAULT_HOST, string $db = DEFAULT_DB){
        /**
         * Starts the class and the connection with a MySQL database.
         * @param string $user The user using for the connection.
         * @param string $passwd The user password.
         * @param string $host The host/IP to connect.
         * @param string $db The database to connect.
         * @throws AlreadyConnectedError If the class already haves a connection running.
         */
        if($this->got_connection) throw new AlreadyConnectedError("There's a connection with a MySQL database already", 1);
        $this->connection = new mysqli($host, $user, $passwd, $db);
        $this->database_connected = $db;
        $this->host_using = $host;
        $this->user_connected = $user;
        $this->got_connection = true;
        $this->connection->autocommit(true);
    }

    public function __destruct(){
        /**
         * Destrois the class and also closes the connection to a MySQL database.
         */
        $this->checkNotConnected();
        $this->connection->close();
        $this->user = "";
        $this->database_connected = "";
        $this->host_using = "";
        $this->got_connection = false;
    }
}

?>