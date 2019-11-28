<?php
namespace Core;

require_once "/var/www/html/lpgp-server/core/Exceptions.php";
// add the logs manager after.

// Session system.
use mysqli;
use mysqli_result;

// Exceptions
use DatabaseActionsExceptions\AlreadyConnectedError;
use DatabaseActionsExceptions\NotConnectedError;

use UsersSystemExceptions\InvalidUserName;
use UsersSystemExceptions\PasswordAuthError;
use UsersSystemExceptions\UserAlreadyExists;
use UsersSystemExceptions\UserNotFound;
use UsersSystemExceptions\UserKeyNotFound;

use ProprietariesExceptions\ProprietaryKey;
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
    protected $connection;
    protected $database_connected;
    protected $host_using;
    protected $got_connection;
    protected $user_connected;

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

class UsersData extends DatabaseConnection{
    /**
     * That class contains the main actions for the users database.
     * @const DATETIME_FORMAT The format for the date in the database.
     */
    const DATETIME_FORMAT = "H:m:i Y-M-d";

    public function __construct(string $usr, string $passwd, string $host = DEFAULT_HOST, string $db = DEFAULT_DB){
        /**
         * Starts the class and the connection with the session handler.
         * The params are the same then at the parent::__construct().
         */
        parent::__construct($usr, $passwd, $host, $db);
        session_start();
    }

    public function __destruct(){
        /**
         * Just the same thing then the parent::__destruct, but implemented the session_handler destructor.
         */
        
        session_unset();
        session_destroy(); 
        parent::__destruct();
    }

    private function checkUserExists(string $username, bool $auto_throw = true){
        /**
         * Checks if a user exists in the database. 
         * @param string $username The user to search in the database.
         * @param bool $auto_throw If the method will throw a exception if the user don't exists.
         * @throws UserNotFound If there's no such user in the database, and the method's allowed to throw the exception.
         * @return bool
         */
        $this->checkNotConnected();
        $qr_all = $this->connection->query("SELECT nm_user FROM tb_users WHERE nm_user = \"$username\";");
        while($row = $qr_all->fetch_array()){
            if($row['nm_user'] == $username) return true;
        }
        if($auto_throw) throw new UserNotFound("There's no user '$username'", 1);
        else return false;
    }

    public function authPassword(string $user, string $password, bool $encoded_password = true){
        /**
         * Authenticate a user password, for login or another simple authentication.
         * @param string $user The user to authenticate
         * @param string $password The user password
         * @param bool $encoded_password If the user password's encoded on the database.
         * @throws PasswordAuthError If the passwords doesn't matches
         * @throws UserNotFound If the selected user don't exists.
         * @return bool
         */
        $this->checkNotConnected();
        if(!$this->checkUserExists($user, false)) throw new UserNotFound("There's no user '$user' in the database", 1);
        $usr_dt  = $this->connection->query("SELECT vl_password FROM tb_users WHERE nm_user = \"$user\";")->fetch_array();
        $from_db = $encoded_password ? base64_decode($usr_dt['vl_password']) : $usr_dt['vl_password'];
        if($password != $from_db) throw new PasswordAuthError("Invalid Password!");
        else return true;
    }

    public function login(string $user, string $password, bool $encoded_password = true){
        /**
         * Makes the login with a user in the database, with a password authentication and login setup.
         * @param string $user The user to make login.
         * @param string $password The user password.
         * @param bool $encoded_password If the user password is encoded in the database.
         * @return void
         */
        $rcv = $this->authPassword($user, $password, $encoded_password);
        $_SESSION['user-logged'] = "true";
        $_SESSION['user'] = $user;
        $_SESSION['mode'] = "normie";
    }

    public function logoff(){
        /**
         * A handler for the method logoff on the attributte session_handler.
         * @return void
         */
        $_SESSION['user-logged'] = "false";
        $_SESSION['user'] = "";
        $_SESSION['mode'] = "";
    }

    public function checkUserKeyExists(string $key){
        /**
         * Checks if a key already haves a user, important to checking user key with email and for the creation of another key.
         * @param string $key The key to search.
         * @author Giulliano Rossi <giulliano.scatalon.rossi@gmail.com>
         * @return bool
         */
        $this->checkNotConnected();
        $qr_wt = $this->connection->query("SELECT vl_key FROM tb_users WHERE vl_key = \"$key\";");
        while($row = $qr_wt->fetch_array()){
            if($row['vl_key'] == $key) return true;
        }
        unset($qr_wt);
        return false;
    }

    public function createUserKey(){
        /**
         * Generate a user key for the database.
         * @return void
         */
        $rand_len = mt_rand(5, 255);
        $key = "";
        while(true){
            $arr = [];
            for($i = 0; $i <= $rand_len; $i++){
                $rand = mt_rand(33, 126);
                array_push(ord($rand));
                unset($rand);   // maybe removed after
            }
            $key = implode("", $arr);
            if(!$this->checkUserKeyExists($key)) return $key;
            else continue;
        }
    }

    public function addUser(string $user, string $password, string $email, bool $encode_password = true){
        /**
         * Adds a user for the database. Normally made for be used in HTML forms
         * @param string $user The name for the user.
         * @param string $password The user password.
         * @param string $email The user email.
         * @param bool $encode_password If the password needs to be encoded or is already encoded.
         * @throws UserAlreadyExists If there's a user with that name already in the database.
         * @return void
         */
        $this->checkNotConnected();
        if($this->checkUserExists($user, false)) throw new UserAlreadyExists("There's already a user with the name '$user'", 1);
        $to_db = $encode_password ? base64_encode($password) : $password;
        $usr_key = $this->createUserKey();
        $cr_dt = date(self::DATETIME_FORMAT);
        $qr = $this->connection->query("INSERT INTO tb_users (nm_user, vl_email, vl_password, vl_key, dt_creation) VALUES (\"$user\", \"$email\", \"$to_db\", \"$usr_key\", \"$cr_dt\");");
        unset($qr);
    }

    public function deleteUser(string $user){
        /**
         * Removes a user from the database.
         * @param string $user the user to remove.
         * @throws UserNotFound If the user selected don't exists in the database.
         * @return void
         */
        $this->checkNotConnected();
        if(!$this->checkUserExists($user)) throw new UserNotFound("There's no user with the name '$user'!", 1);
        $qr_dl = $this->connection->query("DELETE FROM tb_users WHERE nm_user = \"$user\";");
        unset($qr_dl);
    }
    
    public function chUserName(string $user, string $newname){
        /**
         * Changes a user name in the database.
         * @param string $user THe user to change the name
         * @param string $newname The new name of the user
         * @throws UserNotFound If the user selected don't exists.
         * @throws UserAlreadyExists If the name selected is already in use from another user.
         * @return void
         */
        $this->checkNotConnected();
        if(!$this->checkUserExists($user)) throw new UserNotFound("There's no user '$user'", 1);
        if($this->checkUserExists($newname)) throw new UserAlreadyExists("The name '$newname' is already in use", 1);
        $qr = $this->connection->query("UPDATE tb_users SET nm_user = \"$newname\" WHERE nm_user = \"$user\";");
        unset($qr);
    }
    
    public function chUserEmail(string $user, string $new_email){
        /**
         * Changes a user email in the database.
         * @param string $user The user to change the email.
         * @param string $email The new user email.
         * @throws UserNotFound If the user don't exists in the database.
         * @return void
         */
        $this->checkNotConnected();
        if(!$this->checkUserExists($user)) throw new UserNotFound("There's no user '$user'", 1);
        $qr = $this->connection->query("UPDATE tb_users SET vl_email = \"$new_email\" WHERE nm_user = \"$user\";");
        unset($qr);
    }
    
    public function chUserPasswd(string $user, string $new_passwd, bool $encode = true){
        /**
         * Changes the user password, but it need to be authenticated by the user password.
         * @param string $user The user to change the password
         * @param string $new_passwd The new password.
         * @param bool $encode If the method will need to encode the password before updating it, if don't the password need to be encoded on base64
         * @throws UserNotFound If there's no user such the selected in the database.
         * @return void
         */
        $this->checkNotConnected();
        if(!$this->checkUserExists($user)) throw new UserNotFound("There's no user '$user'", 1);
        $to_db = $encode ? base64_encode($new_passwd) : $new_passwd;
        $qr = $this->connection->query("UPDATE tb_users SET vl_password = \"$to_db\" WHERE nm_user = \"$user\";");
        unset($qr);
        unset($to_db);
    }
    
    public function setUserChecked(string $user, bool $checked = true){
        /**
         * Sets if a user haves the email checked in the database.   
         */
    }
}
?>
