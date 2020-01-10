<?php
namespace Core;
// TODO: Fix the php docs (they're upside down)
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Exceptions.php";
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

use ProprietariesExceptions\ProprietaryKeyNotFound;
use ProprietariesExceptions\AuthenticationError;
use ProprietariesExceptions\InvalidProprietaryName;
use ProprietariesExceptions\ProprietaryNotFound;
use ProprietariesExceptions\ProprietaryAlreadyExists;

use SignaturesExceptions\InvalidSignatureFile;
use SignaturesExceptions\SignatureAuthError;
use SignaturesExceptions\SignatureNotFound;
use SignaturesExceptions\SignatureFileNotFound;
use SignaturesExceptions\VersionError;

define("DEFAULT_HOST", "localhost");
define("DEFAULT_DB", "LPGP_WEB");
define("ROOT_VAR", $_SERVER['DOCUMENT_ROOT']);
define("EMAIL_USING", "lpgp@gmail.com");
define("DEFAULT_USER_ICON", $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/media/user-icon.png");
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
        mysqli_close($this->connection);
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
    const DATETIME_FORMAT = "H:m:i Y-j-d";
    const EMAIL_USING     = "lpgp@gmail.com";

    public function __construct(string $usr, string $passwd, string $host = DEFAULT_HOST, string $db = DEFAULT_DB){
        /**
         * Starts the class and the connection with the session handler.
         * The params are the same then at the parent::__construct().
         */
        parent::__construct($usr, $passwd, $host, $db);
    }

    public function __destruct(){
        /**
         * Just the same thing then the parent::__destruct, but implemented the session_handler destructor.
         */
        parent::__destruct();
    }

    private function checkUserExists(string $username, bool $auto_throw = false){
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

    /**
     * Authenticate the user key at the database.
     *
     * @param string $username The user that's authenticating the account.
     * @param string $key The key received from the user
     * @return bool
     */
    public function authUserKey(string $username, string $key){
        $this->checkNotConnected();
        if(!$this->checkUserExists($username)) throw new UserNotFound("There's no user '$username'!", 1);
        $usr_data = $this->connection->query("SELECT * FROM tb_users WHERE nm_user = \"$username\";")->fetch_array();
        return $key == $usr_data['vl_key'];
    }

    public function login(string $user, string $password, bool $encoded_password = true){
        /**
         * Makes the login with a user in the database, with a password authentication and login setup.
         * @param string $user The user to make login.
         * @param string $password The user password.
         * @param bool $encoded_password If the user password is encoded in the database.
         * @return array
         */
        $rcv = $this->authPassword($user, $password, $encoded_password);
        $checked_usr = $this->connection->query("SELECT checked FROM tb_users WHERE nm_user = \"$user\";")->fetch_array();
        $img_path = $this->connection->query("SELECT vl_img FROM tb_users WHERE nm_user = \"$user\";")->fetch_array();
        $arr_info = [];
        $arr_info['user-logged'] = "true";
        $arr_info['user'] = $user;
        $arr_info['mode'] = "normie";
        $arr_info['user-icon'] = $img_path['vl_img'];
        $arr_info['checked'] = $checked_usr['checked'] == "1" || $checked_usr['checked'] == 1? "true": "false";
        return $arr_info;
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
        $rand_len = mt_rand(1, 5);
        $key = "";
        while(true){
            $arr = array();
            for($i = 0; $i <= $rand_len; $i++){
                $rand = mt_rand(33, 126);
                $arr[] = ord($rand);
                unset($rand);   // maybe removed after
            }
            $key = implode("", $arr);
            if(!$this->checkUserKeyExists($key)) return $key;
            else continue;
        }
    }

    public function addUser(string $user, string $password, string $email, bool $encode_password = true, string $img){
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
        $qr = $this->connection->query("INSERT INTO tb_users (nm_user, vl_email, vl_password, vl_key, vl_img) VALUES (\"$user\", \"$email\", \"$to_db\", \"$usr_key\", \"$img\");");
        if(!$qr) echo mysqli_error($this->connection);
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

    /**
     * Changes the User image at the database.
     * @param string $user The user to change the image.
     * @param string $new_img The new image path
     * @throws UserNotFound If there's no user with the given name.
     * @return void
     */
    public function chImage(string $user, string $new_img = DEFAULT_USER_ICON){
        $this->checkNotConnected();
        if(!$this->checkUserExists($user, false)) throw new UserNotFound("There's no user '$user'", 1);
        $qr = $this->connection->query("UPDATE tb_users SET vl_img  = \"$new_img\" WHERE nm_user = \"$user\";");
        unset($qr);
    }
    
    public function setUserChecked(string $user, bool $checked = true){
        /**
         * Sets if a user haves the email checked in the database.   
	     */
	    $this->checkNotConnected();
        if(!$this->checkUserExists($user)) throw new UserNotFound("There's no user '$user'!", 1);
        $to_db = $checked ? 1 : 0;
        $qr = $this->connection->query("UPDATE tb_users SET checked = $to_db WHERE nm_user = \"$user\";");
        unset($qr);
        unset($to_db);
    }

    public function checkUserCheckedEmail(string $user){
        /**
         * Checks if the user haves the email checked on the database.
         * Checking the field 'checked' on the MySQL Database.
         * @param string $user The user to check
         * @throws UserNotFound If the user don't exists
         * @return bool
         */
        $this->checkNotConnected();
        if(!$this->checkUserExists($user)) throw new UserNotFound("There's no user '$user'!", 1);
        $usr_data = $this->connection->query("SELECT checked FROM tb_users WHERE nm_user = \"$user\";")->fetch_array();
        return $usr_data['checked'] == 1;
    }

    public function fetchTemplateEmail(string $user, string $key){
        /**
         * That function returns the content of the email template to send in HTML.
         * Wich template will be used to send the checking email, it will replace
         * The username and the user key.
         *
         * @param string $user The user that the server will send the email.
         * @param string $key The user key, storaged at the database.
         * @return string
         */
        $raw_content = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/templates/template-email.html");
        $cont1 = str_replace("%user%", $user, $raw_content);
        return str_replace("%key%", $key, $cont1);
    }


    public function sendCheckEmail(string $user){
        /**
         * Sends a email to the selected user.
         * That email will contain the users key storaged on the database.
         * @param string $user The user to send the checking email
         * @throws UserNotFound If the selected/referencied user don't exists.
         * @return bool
         */
        $this->checkNotConnected();
        if(!$this->checkUserExists($user)) throw new UserNotFound("There's no user '$user'!", 1);
        $usr_data = $this->connection->query("SELECT vl_key, vl_email, checked FROM tb_users WHERE nm_user = \"$user\";")->fetch_array();
        if($usr_data['checked'] == 1) return true;  // will end the execution
        $headers = "MIME-Version: 1.0\n";
        $headers .= "Content-Type: text/html; charset=iso-8859-1\n";
        $headers .= "From: " . self::EMAIL_USING . "\n";
        $headers .= "Cc: " . $usr_data['vl_email'] . "\n";
        $content = $this->fetchTemplateEmail($user, $usr_data['vl_key']);
        return mail($usr_data['vl_email'], "Your LPGP account!", $content, $headers);
    }

    public function qrUserByName(string $name_needle, bool $exactly = false){
        /**
         * Query all the users by the name.
         * @param string $name_needle The string to search
         * @param bool $exactly If the method will search for te exact string in the database.
         * @return array  in that array will have all the names.
         */
        $this->checkNotConnected();
        $arr = array();
        if($exactly) $qr = $this->connection->query("SELECT nm_user FROM tb_users WHERE nm_user = \"$name_needle\";");
        else $qr = $this->connection->query("SELECT nm_user FROM tb_users WHERE nm_user LIKE \"%$name_needle%\";");
        while($row = $qr->fetch_array()) array_push($arr, $row['nm_user']);
        return $arr;
    }

    public function qrUserByEmail(string $email_needle, bool $exactly = false){
        /**
         * Searchs all the users with a string in the email.
         * @param string $email_needle The string to search on the email field
         * @param bool $exactly Searchs for the exact string in the email.
         * @return array
         */
        $this->checkNotConnected();
        $arr = array();
        if($exactly) $qr = $this->connection->query("SELECT nm_user FROM tb_users WHERE vl_email = \"$email_needle\";");
        else $qr = $this->connection->query("SELECT nm_user FROM tb_users WHERE vl_email LIKE \"%$email_needle%\";");
        while($row = $qr->fetch_array()) array_push($arr, $row['nm_user']);
        return $arr;
    }

    public function qrUserByKey(string $key_needle, bool $exactly = false){
        /**
         * Searches the user name by a string on him key, it'll be used at the web, but at the admin on the server.
         * @param string $key_needle The string to search on the key field;
         * @param bool $exactly If the search will be the exactly the string.
         * @return array.
         */
        $this->checkNotConnected();
        $arr = array();
        if($exactly) $qr = $this->connection->query("SELECT nm_user FROM tb_users WHERE vl_key = \"$key_needle\";");
        else $qr = $this->connection->query("SELECT nm_user FROM tb_users WHERE vl_key LIKE \"%$key_needle%\";");
        while($row = $qr->fetch_array()) array_push($arr, $row['nm_user']);
        return $arr;
    }
}

class ProprietariesData extends DatabaseConnection{
    /**
     * That class contains the main actions with the propriearies on the system.
     * The main methods to manage the proprietaries accounts in the database are here.
     * The constants are the same then the in UsersData class
     */

    const DATETIME_FORMAT = "H:m:i Y-M-d";
    const EMAIL_USING     = "lpgp@gmail.com";

     private function checkProprietaryExists(string $nm_proprietary){
         /**
          * Checks if a proprietary account exists in the database.
          * @param string $nm_proprietary The name of the proprietary to search.
          * @return bool
          */
          $this->checkNotConnected();
          $qr = $this->connection->query("SELECT nm_proprietary FROM tb_proprietaries WHERE nm_proprietary = \"$nm_proprietary\";");
          while($row = $qr->fetch_array()){
              if($row['nm_proprietary'] == $nm_proprietary) return true;
              else continue;
          }
          return false;
     }


    public function checkProprietaryKeyExists(string $key){
        /**
         * Checks if a key already haves a user, important to checking user key with email and for the creation of another key.
         * @param string $key The key to search.
         * @author Giulliano Rossi <giulliano.scatalon.rossi@gmail.com>
         * @return bool
         */
        $this->checkNotConnected();
        $qr_wt = $this->connection->query("SELECT vl_key FROM tb_proprietaries WHERE vl_key = \"$key\";");
        while($row = $qr_wt->fetch_array()){
            if($row['vl_key'] == $key) return true;
        }
        unset($qr_wt);
        return false;
    }

    public function createProprietaryKey(){
        /**
         * Generate a user key for the database.
         * @return void
         */
        $rand_len = mt_rand(1, 5);
        $key = "";
        while(true){
            $arr = [];
            for($i = 0; $i <= $rand_len; $i++){
                $rand = mt_rand(33, 126);
                $arr[] = ord($rand);
                unset($rand);   // maybe removed after
            }
            $key = implode("", $arr);
            if(!$this->checkProprietaryKeyExists($key)) return $key;
            else continue;
        }
    }

    /**
     * That function checks if the key received is the same key then the proprietary key at
     * the database, important for validate the proprietary email.
     *
     * @param string $proprietary That's checking the key
     * @param string $key_rcv The key received.
     * @throws ProprietaryNotFound If the proprietary don't exists.
     * @return bool If the key is valid or not.
     */
    public function authPropKey(string $proprietary, string $key_rcv){
        $this->checkNotConnected();
        if(!$this->checkProprietaryExists($proprietary)) throw new ProprietaryNotFound("There's no proprietary '$proprietary'!", 1);
        $prop_data = $this->connection->query("SELECT vl_key, checked FROM tb_proprietaries WHERE nm_proprietary = \"$proprietary\";")->fetch_array();
        if($prop_data['checked'] == 1) return null;  // if the proprietary email was checked already
        return $prop_data['vl_key'] == $key_rcv;
    }

    public function authPasswd(string $proprietary, string $password, bool $encoded_password = true){
         /**
          * Authenticates a proprietary user password, that will be used for every thing, even the user data change.
          *
          * @param string $proprietary The proprietary user to authenticate the password.
          * @param string $password The proprietary password, from a input.
          * @param bool $encoded_password If the password is enconded at the database, by default yes.
          * @throws ProprietaryNotFound If the selected proprietary don't exists.
          * @return bool
          */
        $this->checkNotConnected();
        if(!$this->checkProprietaryExists($proprietary)) throw new ProprietaryNotFound("There's no proprietary user '$proprietary'!", 1);
        $prop_data = $this->connection->query("SELECT vl_password FROM tb_proprietaries WHERE nm_proprietary = \"$proprietary\";")->fetch_array();
        $from_db = $encoded_password ? base64_decode($prop_data['vl_password']) : $prop_data['vl_password'];
        return $password == $from_db;
     }

     /**
      * Makes the authentication and sets the $_SESSION keys to do the login.
      * Just like the UsersData->login function.
      *
      * @param string $proprietary The proprietary that will do the login.
      * @param string $password The password received from the input at the form
      * @param bool $encoded_password If the password is encoded at the database.
      * @throws ProprietaryNotFound If there's no proprietary such the selected
      * @throws AuthenticationError If the password's incorrect
      * @return array
      */
    public function login(string $proprietary, string $password, bool $encoded_password = true){
        $this->checkNotConnected();
        $auth = $this->authPasswd($proprietary, $password, $encoded_password);
        if(!$auth) throw new AuthenticationError("Invalid password", 1);
        $arr_info = [];
        $checked = $this->connection->query("SELECT checked, vl_img FROM tb_proprietaries WHERE nm_proprietary = \"$proprietary\";")->fetch_array();
        $arr_info['user'] = $proprietary;
        $arr_info['mode'] = "prop";
        $arr_info['user-logged'] = "true";
        $arr_info['checked'] = $checked['checked'] == 1 || $checked == "1" ? "true" : "false";
        $arr_info['user-icon'] = $checked['vl_img'];
        unset($auth);   // min use of memory
        return $arr_info;
     }

     /**
      * Adds a proprietary account in the database, that will be automaticly commited to the MySQL database.
      * @param string $prop_name The proprietary account name.
      * @param string $password The account password.
      * @param bool $encode_password If the method will encode the password before going to the database, if don't the password need to be in bas64.
      * @param string $img_path The path to the image file of the user avatar
      * @throws ProprietaryAlreadyExists If there's a proprietary with that name already.
      * @return void
      */
    public function addProprietary(string $prop_name, string $password, string $email, bool $encode_password = true, string $img = DEFAULT_USER_ICON){
        $this->checkNotConnected();
        if($this->checkProprietaryExists($prop_name)) throw new ProprietaryAlreadyExists("There's the proprietary '$prop_name' already", 1);
        $to_db = $encode_password ? base64_encode($password) : $password;
        $prop_key = $this->createProprietaryKey();
        $qr = $this->connection->query("INSERT INTO tb_proprietaries (nm_proprietary, vl_email, vl_password, vl_key, vl_img) VALUES (\"$prop_name\", \"$email\", \"$to_db\", \"$prop_key\", \"$img\");");
        unset($to_db);
     }

     /**
      * Removes a proprietary account from the database.
      * @param string $proprietary The account name to remove.
      * @throws ProprietaryNotFound If the proprietary selected don't exists
      * @return void
      */
    public function delProprietary(string $proprietary){
        $this->checkNotConnected();
        if(!$this->checkProprietaryExists($proprietary)) throw new ProprietaryNotFound("There's no proprietary account '$proprietary'", 1);
        $qr_del = $this->connection->query("DELETE FROM tb_proprietaries WHERE nm_proprietary = \"$proprietary\";");
        unset($qr_del);
     }

     /**
      * Changes a proprietary account name.
      * @param string $proprietary The proprietary account to change the name (name)
      * @param string $new_name The new account name
      * @throws ProprietaryNotFound If the proprietary selected don't exists in the database.
      * @throws ProprietaryAlreadyExists If the new name is already beeing used by another account.
      * @return void
      */
    public function chProprietaryName(string $proprietary, string $new_name){
        $this->checkNotConnected();
        if(!$this->checkProprietaryExists($proprietary)) throw new ProprietaryNotFound("There's no proprietary account '$proprietary'", 1);
        if($this->checkProprietaryExists($new_name)) throw new ProprietaryAlreadyExists("The name '$new_name' is already in use, choose another", 1);
        $qr_ch = $this->connection->query("UPDATE tb_proprietaries SET nm_proprietary = \"$new_name\" WHERE nm_proprietary = \"$proprietary\";");
        unset($qr_ch);
     }

     /**
      * Changes a proprietary email account.
      * 
      * @param string $proprietary The proprietary to change the email.
      * @param string $new_email The new value for the email
      * @throws ProprietaryNotFound If the proprietary selected don't exists in the database
      * @return void
      */
    public function chProprietaryEmail(string $proprietary, string $new_email){
        $this->checkNotConnected();
        if(!$this->checkProprietaryExists($proprietary)) throw new ProprietaryNotFound("There's no proprietary '$proprietary'", 1);
        $qr_ch = $this->connection->query("UPDATE tb_proprietaries SET vl_email = \"$new_email\" WHERE nm_proprietary = \"$proprietary\";");
        unset($qr_ch);
     }

     /**
      * Changes the proprietary use avatar image.
      * @param string $proprietary The name of the proprietary to change the image
      * @param string $new_img The new image for the user icon.
      * @throws ProprietaryNotFound If there's no proprietary with the $proprietary name
      * @return void
      */
    public function chImage(string $proprietary, string $new_img = DEFAULT_USER_ICON){
        $this->checkNotConnected();
        if(!$this->checkProprietaryExists($proprietary)) throw new ProprietaryNotFound("There's no proprietary '$proprietary'", 1);
        $qr = $this->connection->query("UPDATE tb_proprietaries SET vl_img = \"$new_img\" WHERE nm_proprietary = \"$proprietary\";");
        unset($qr);
    }

     /**
      * Changes a proprietary account password, but remember to use it after the authentication (obviously)
      *
      * @param string $proprietary The proprietary to change the password.
      * @param string $new_passwd The new account password
      * @param bool $encode_passwd If the method will encode the password in base64
      * @throws ProprietaryNotFound If the selected account ($proprietary) don't exists
      * @return void
      */
    public function chProprietaryPasswd(string $proprietary, string $new_passwd, bool $encode_passwd = true){
        $this->checkNotConnected();
        if(!$this->checkProprietaryExists($proprietary)) throw new ProprietaryNotFound("There's no proprietary '$proprietary'", 1);
        $to_db = $encode_passwd ? base64_encode($new_passwd) : $new_passwd;
        $qr_ch = $this->connection->query("UPDATE tb_proprietaries SET vl_password = \"$to_db\" WHERE nm_proprietary = \"$proprietary\";");
        unset($to_db);
        unset($qr_ch);
     }

     /**
      * Changes the field checked, used when the key was sended and used at the email. Or when he changes him email.
      * 
      * @param string $proprietary The proprietary to change the info.
      * @param bool   $checked     If the email was checked already.
      * @throws ProprietaryNotFound If the choosed account don't exists in the database.
      * @return void
      */
    public function setProprietaryChecked(string $proprietary, bool $checked = true){
        $this->checkNotConnected();
        if(!$this->checkProprietaryExists($proprietary)) throw new ProprietaryNotFound("There's no proprietary '$proprietary'", 1);
        $checked_vl = $checked ? 1: 0;
        $qr_ch = $this->connection->query("UPDATE tb_proprietaries SET checked = $checked_vl WHERE nm_proprietary = \"$proprietary\";");
        unset($checked_vl);
        unset($qr_ch);
     }

     /**
      * Sets special names on the HTML file to be used to send the email with the login key.
      * On the HTML file the special names useds are:
      *     * %user% => The proprietary using (or any another user)
      *     * %key% => The account key.
      * @param string $prop The proprietary name to stay on the %user%
      * @param string $key  The proprietary key
      * @return string
      */
    public function parseHTMLTemplateEmailK(string $prop, string $key, string $path){
        $content = file_get_contents($path);
        $r1_content = str_replace("%user%", $prop, $content);
        return str_replace("%key%", $key, $r1_content);
     }

     /**
      * That function sends  a email with the code to the proprietary email. That uses the method mail, and requires the SMTP of the GMAIL.
      * Also that function calls a method to convert the HTML file to the content. 
      * 
      * @param string $proprietary The proprietary to get the data and send the email.
      * @throws ProprietaryNotFound If the selected proprietary don't exists in the database.
      * @return bool If the email was sended, or if the account already checked the email.
      */
    public function sendCheckEmail(string $proprietary){
        $this->checkNotConnected();
        if(!$this->checkProprietaryExists($proprietary)) throw new ProprietaryNotFound("There's no proprietary account '$proprietary'", 1);
        $prop_dt = $this->connection->query("SELECT vl_key, checked, vl_email FROM tb_proprietaries WHERE nm_proprietary = \"$proprietary\";")->fetch_array();
        $content = $this->parseHTMLTemplateEmailK($proprietary, $prop_dt['vl_key'], $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/templates/template-email.html");
        $headers = "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\n";
        $headers .= "From: " . self::EMAIL_USING . "\n";
        $headers .= "Cc: " . $prop_dt['vl_email'] . "\n";
        return mail($prop_dt['vl_email'], "Your LPGP key!", $content, $headers);
     }

     /**
      * Searches in the database for a proprietary with a name like a string or a name exactly equal a string.
      * 
      * @param string $name_needle The string to search in the names.
      * @param bool $exactly If will be for the exactly equal names.
      * @return array
      */
    public function qrPropByName(string $name_needle, bool $exactly = false){
        $this->checkNotConnected();
        $results = array();
        if($exactly) $qr = $this->connection->query("SELECT nm_proprietary FROM tb_proprietaries WHERE nm_proprietary = \"$name_needle\";");
        else $qr = $this->connection->querY("SELECT nm_proprietary FROM tb_proprietaries WHERE nm_proprietary LIKE  \"%$name_needle%\";");
        while($row = $qr->fetch_array()) array_push($results, $row['nm_proprietary']);
        return $results;
     }

     /**
      * Searches a proprietary for a string in the email field at the database.
      *
      * @param string $email_needle The string to search at the email.
      * @param bool $exactly If will search for the exactly string in the database.
      * @return array
      */
    public function qrPropByEmail(string $email_needle, bool $exactly = false){
        $this->checkNotConnected();
        $results = array();
        if($exactly) $qr = $this->connection->query("SELECT nm_proprietary FROM tb_proprietaries WHERE vl_email = \"$email_needle\";");
        else $qr = $this->connection->query("SELECT nm_proprietary FROM tb_proprietaries WHERE vl_email LIKE \"%$email_needle%\";");
        while($row = $qr->fetch_array()) array_push($results, $row['nm_proprietary']);
        return $results;
     }
    
}

/**
 * That class contains all the uses of the signatures and signatures files.
 * The uploaded files stay at the directory ./usignatures.d and the downloadeble files stay at
 * the directory ./signatures.d
 * 
 * @var string|int VERSION_ACT The version the signature will be storaged.
 * @var string|int VERSION_MIN The minimal version accepted.
 * @var array      VERSION_ALL The allowed versions of reading.
 */
class SignaturesData extends DatabaseConnection{
    const VERSION_ACT = "alpha";
    const VERSION_MIN = "alpha";
    const VERSION_ALL = ["alpha"];
    const CODES       = ["md5", "sha1", "sha256"];
    const DELIMITER   = "/";


    private function checkSignatureExists(int $signature_id){
        /**
         * Checks if a signature exists in the database. It uses the PK at the database.
         * 
         * @param int $signature_id The PK for search.
         * @return bool
         */
        $this->checkNotConnected();
        $qr = $this->connection->query("SELECT cd_signature FROM tb_signatures WHERE cd_signature = $signature_id;");
        while($row = $qr->fetch_array()){
            if($row['cd_signature'] == $signature_id || $row['cd_signature'] == "" . $signature_id) return true;
        }
        unset($qr);
        return false;
    }

    public static function generateFileNm(int $initial_counter = 0){
        /**
         * Creates a filename for the signature file. 
         *
         * @param int $initial_counter The first contage of the filename (signature-file-$initial_counter)
         * @return string
         */
        $local_counter = $initial_counter;
        while(true){
            if(!file_exists($_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/signatures.d/signature-file-". $local_counter . ".lpgp")) 
                break;
            else $local_counter++;
        }
        return "signature-file-".$local_counter . ".lpgp";
    }

    public function createsSignatureFile(int $signature_id, bool $HTML_mode = false, string $file_name){
        /**
         * Creates a signature file and return it link to the file.
         * 
         * @param string $signature_id The PK on the database.
         * @param bool $HTML_mode If the method will return a HTML <a>
         * @throws SignatureNotFound If there's no such PK in the database.
         * @return string
         */
        $this->checkNotConnected();
        if(!$this->checkSignatureExists($signature_id)) throw new SignatureNotFound("There's no signature #$signature_id !", 1);
        $sig_dt = $this->connection->query("SELECT prop.nm_proprietary, sig.vl_password, sig.vl_code FROM tb_signatures as sig INNER JOIN tb_proprietaries AS prop ON prop.cd_proprietary = sig.id_proprietary WHERE sig.cd_signature = $signature_id;")->fetch_array();
        $content = array(
            "Version" => self::VERSION_ACT,
            "Proprietary" => $sig_dt['nm_proprietary'],
            "ID" => $signature_id,
            "Signature" => $sig_dt['vl_password']
        );   // encoded on JSON format after
        $to_json = json_encode($content);
        $arr_ord = array();
        for($char = 0; $char < strlen($to_json); $char++) array_push($arr_ord, "" . ord($to_json[$char]));
        $content_file = implode(self::DELIMITER, $arr_ord);
        $root = $_SERVER['DOCUMENT_ROOT'];
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/signatures.d/" . $file_name, $content_file);
        return $HTML_mode ? "<a href=\"http://localhost/lpgp-server/signatures.d/$file_name\" download>Get your signature #$signature_id here!</a>" : "$root/lpgp-server/signatures.d/$file_name";
    }


    private static function checkFileValid(string $file_name){
        /**
         * Checks if the signature file is a .lpgp file.
         *
         * @param string $file_name The file to verify
         * @return bool
         */
        $sp = explode(".", $file_name);
        return $sp[count($sp) - 1] == "lpgp";
    }

    public function checkSignatureFile(string $file_name){
        /**
         * Checks a uploaded signature file. It needs to have the extension .lpgp.
         * All the uploaded signatures files stay at the usignatures.d.
         * 
         * @param string $file_path The signature file uploaded path.
         * @throws InvalidSignatureFile if the file is not a .lpgp
         * @throws VersionError if the signature file version is not allowed.
         * @throws SignatureNotFound if the ID of the signature on the file don't exists 
         * @throws SignatureAuthError If the file is not valid
         * @return true
         */
        $this->checkNotConnected();
        if(!$this->checkFileValid($file_name)) throw new InvalidSignatureFile("", 1);
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/usignatures.d/$file_name")) throw new SignatureFileNotFound("There's no file '$file_name' on the uploaded signatures folder.", 1);
        $content_file = utf8_encode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/usignatures.d/" . $file_name));
        $sp_content = explode(self::DELIMITER, $content_file);
        $ascii_none = array();
        for($i = 0; $i <= count($sp_content); $i++) {
            array_push($ascii_none, chr((int)$sp_content[$i]));
        }
        $ascii_none_str = implode("", $ascii_none);
        $json_arr = json_decode(preg_replace("/[[[:cntrl:]]/", "", $ascii_none_str), true);
        // if(!in_array($json_arr['Version'], self::VERSION_ALL)) throw new VersionError("The version used by the file is not valid!", 1);
        if(!$this->checkSignatureExists((int) $json_arr['ID'])) throw new SignatureNotFound("There's no signature #" . $json_arr['Signature'], 1);
        $signautre_data = $this->connection->query("SELECT vl_password FROM tb_signatures WHERE cd_signature = " . $json_arr['ID'])->fetch_array();
        if($signautre_data['vl_password'] != $json_arr['Signature']) throw new SignatureAuthError("The file signature is not valid.", 1);
        return true;
    }

    private function checkProprietaryExists(int $id){
        /**
         * Does the same thing then the checkProprietaryExists on the class ProprietariesData, 
         * But this time it uses the PK not the name.
         *
         * @param int $id The PK of the proprietary
         * @return bool
         */
        $this->checkNotConnected();
        $all_rt = $this->connection->query("SELECT cd_proprietary FROM tb_proprietaries WHERE cd_proprietary = $id;");
        while($row = $all_rt->fetch_array()){
            if($row['cd_proprietary'] == $id) return true;
        }
        unset($all_rt);
        return false;
    }

    public function addSignature(int $id_proprietary, string $password, int $code, bool $encode_word = true){
        /**
         * Creates a new signature on the database.
         * @param int $id_proprietary The PK of the signature proprietary.
         * @param string $password The word used to be the signature.
         * @param int $code The algo index on the constant self::CODES
         * @param bool $encode_word If the method will encode the signature
         * @throws ProprietaryNotFound if the $id_proprietary don't exists as a proprietary
         * @return void
         */
        $this->checkNotConnected();
        if(!$this->checkProprietaryExists($id_proprietary)) throw new ProprietaryNotFound("There's no proprietary with the ID #$id_proprietary", 1);
        $to_db = $encode_word ? hash(self::CODES[$code], $password) : $password;
        $qr_vd = $this->connection->query("INSERT INTO tb_signatures (id_proprietary, vl_password, vl_code) VALUES ($id_proprietary, \"$to_db\", $code);");
        unset($qr_vd);
        unset($to_db);
    }

    public function delSignature(int $signature_id){
        /**
         * Removes a signature from the database. It uses the PK of the signature tuple at the MySQL database.
         * 
         * @param int $signature_id The signature PK on the database.
         * @throws SignatureNotFound If the PK don't exists in the database.
         * @return void
         */
        $this->checkNotConnected();
        if(!$this->checkSignatureExists($signature_id)) throw new SignatureNotFound("There's no signature with the PK #$signature_id", 1);
        $qr_rm = $this->connection->query("DELETE FROM tb_signatures WHERE cd_signature = $signature_id;");
        unset($qr_rm);
    }

    public function chProprietaryId(int $signature, int $new_proprietary){
        /**
         * Changes the FK of the database, that contains the proprietary that owns the signature.
         * 
         * @param int $signature The PK of the signature.
         * @param int $new_proprietary The new Proprietary ID
         * @throws ProprietaryNotFound If the new ID don't exists has a proprietary
         * @throws SignatureNotFound If the PK don't exists.
         * @return void
         */
        $this->checkNotConnected();
        if(!$this->checkSignatureExists($signature)) throw new SignatureNotFound("There's no signature #$signature;", 1);
        if(!$this->checkProprietaryExists($new_proprietary)) throw new ProprietaryNotFound("There's no proprietary with that id #$new_proprietary", 1);
        $qr_ch = $this->connection->query("UPDATE tb_signatures SET id_proprietary = $new_proprietary WHERE cd_signature = $signature;");
        unset($qr_ch);
    }

    public function chSignatureCode(int $signature, int $code, string $word_same){
        /**
         * Changes the algo code used at the signature.
         * 
         * @param int $signature The PK for the signature.
         * @param int $code The index of the constant array self::CODES.
         * @param string $word_same The same word in the database. To reupdate the word too. It don't have to be encoded before.
         * @throws SignatureNotFound If the PK don't exists 
         * @return void
         */
        $this->checkNotConnected();
        if(!$this->checkSignatureExists($signature)) throw new SignatureNotFound("There's no signature #$signature", 1);
        $act_code = $this->connection->query("SELECT vl_code FROM tb_signatures WHERE cd_signatures = $signature;")->fetch_array();
        $to_db = hash(self::CODES[(int) $act_code['vl_code']], $word_same);
        $qr_ch = $this->connection->query("UPDATE tb_signatures SET vl_code = $code WHERE cd_signature = $signature;");
        $qr_ch = $this->connection->query("UPDATE tb_signatures SET vl_password = \"$to_db\" WHERE cd_signature = $signature;");
        unset($qr_ch);
    }

    public function chSignaturePassword(int $signature, string $word, bool $encode_here = true){
        /**
         * It changes the main word of the signature. If the new word is not encoded at the same algo, the method
         * will encode it.
         * 
         * @param int $signature The PK of the signature at the database;
         * @param string $word The new word to set.
         * @param bool $encode_here If the method will encode the word, if don't (false) the word must be encoded already.
         * @throws SignatureNotFound If the PK don't exists.
         * @return void
         */
        $this->checkNotConnected();
        if(!$this->checkSignatureExists($signature)) throw new SignatureNotFound("There's no signature #$signature", 1);
        $to_db = "";
        if($encode_here){
            $code_arr = $this->connection->query("SELECT vl_code FROM tb_signatures WHERE cd_signature = $signature;")->fetch_array();
            $to_db = hash(self::CODES[(int) $code_arr['vl_code']], $word);
        }
        else $to_db = $word;
        $qr_ch = $this->connection->query("UPDATE tb_signatures SET vl_password = \"$to_db\" WHERE cd_signature = $signature;");
        unset($qr_ch);
        unset($to_db);
    }

    public function qrSignatureProprietary(int $proprietary_neddle){
        /**
         * Searches in the database for a singature wich the proprietary FK is the same as the parameter
         * 
         * @param int $proprietary_needle The FK to search
         * @return array|null
         */
        $this->checkNotConnected();
        $qr_all = $this->connection->query("SELECT cd_signature FROM tb_signatures WHERE id_proprietary = $proprietary_neddle");
        $results = array();
        while($row = $qr_all->fetch_array()) array_push($results, $row['cd_signature']);
        return count($results) <= 0 ? null : $results;
    }

    public function qrSignatureAlgo(int $code){
        /**
         * Searches in the database for a signature wich the vl_code is the same as the parameter
         * 
         * @param int $code The vl_code to search
         * @return array|null
         */
        $this->checkNotConnected();
        $results = [];
        $qr_all = $this->connection->query("SELECT cd_signature FROM tb_signatures WHERE vl_code = $code");
        while($row = $qr_all->fetch_array()) array_push($results, $row['cd_signature']);
        return count($results) <= 0 ? null : $results;
    }

    /**
     * That method sends a e-mail for all the users and proprietaries alerting then that had a change on a signature, with a link to dowload then.
     * 
     * @param int $proprietary The proprietary wich changed the signature.
     * @param int $signature_id The signature that the proprietary changed
     * @param array|null $exceptions The users to not send the email
     * @throws SignatureNotFound If the signature don't exists
     * @throws ProprietaryNotFound If the proprietary don't exists,
     * @return void;
     */
    public function sendChSignatureMail(int $proprietary, int $signature_id, string $html_template){
        $this->checkNotConnected();
        if(!$this->checkSignatureExists($signature_id)) throw new SignatureNotFound("The signature #$signature_id don't exists", 1);
        if(!$this->checkProprietaryExists($proprietary)) throw new ProprietaryNotFound("There's no proprietary with the PK #$proprietary", 1);
        $content_raw = file_get_contents($html_template);
        $qr_prp = $this->connection->query("SELECT nm_proprietary FROM tb_proprietaries WHERE cd_proprietary = $proprietary;")->fetch_array();
        $content_1 = str_replace("%prop%", $qr_prp['nm_proprietary'], $content_raw);
        $content_full = str_replace("%signature%", $signature_id, $content_1);
        $all_usr = $this->connection->query("SELECT vl_email FROM tb_users;");
        $all_prop = $this->connection->query("SELECT vl_email FROM tb_proprietaries WHERE cd_proprietary != $proprietary");
        $headers = "MIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1\nFrom: " . EMAIL_USING . "\n";
        while($row = $all_usr->fetch_array()) mail($row['vl_email'],"Signature Update", $content_full, $headers);
        while($row = $all_prop->fetch_array()) mail($row['vl_email'], "Signature Update", $content_full, $headers);
    }
}
// Added after
namespace templateSystem;
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Exceptions.php";

use ExctemplateSystem\AlreadyLoadedFile;
use ExctemplateSystem\InvalidFileType;
use ExctemplateSystem\NotLoadedFile;

/**
 * That class is used to fetch HTML templates at the system, that class works with the errors pages.
 * 
 * There's no one especific error, but every big error will be handled by that.
 * 
 * That just works replacing strings for other values.
 * 
 * There's reserved names at the template, wich can be spoted by the %% at the first and the last character.
 * ---------------------------------------------------------------------------------------------------------
 * Those reserved names/words are:\n
 *      * %message% => The error message, to handling the values
 *      * %file% => The file that was fetching the template
 *      * %line% => The line in the file that the exception was throwed
 *      * %image% => The image that will be showing at the error page
 *      * %title% => The error title, it can be a 500 error or even a login error.
 *      * %btn_rt% => A HTML button to return to some previous page. By default it returns to the index
 * 
 * @var string $page_templated The HTML file path, that's the template. By default is the core/templates/500-error-internal.html
 * @var string $error_message The error message to be showed on the template.
 * @var string|null $file_throwed The file that fetched the template.
 * @var string|int|null $line_error The line of the error at the file.
 * @var string $btn_rt The button to return to the previous page.
 * @var bool $got_document If the class haves a HTML document parsed already. Default = false
 * @var string|null $content The parsed file content
 * @author Giulliano Rossi <giulliano.scatalon.rossi@gmail.com>
 * @access public
 */
class ErrorTemplate{
    private $page_templated;
    private $error_message;
    private $file_throwed;
    private $line_error;
    private $btn_rt = "<button class=\"default-btn btn darkble-btn\" onclick=\"window.location.replace('http://localhost/lpgp-server/');\">Return to the index</button>";
    private $got_document = false;
    private $content;

    /**
     * Checks if the selected file is a HTML file.
     *
     * @param string $file_path The file path to check
     * @author Giulliano Rossi <giulliano.scatalon.rossi@gmail.com>
     * @return bool
     */
    private static function checkFileValid(string $file_path){
        $exp = explode(".", $file_path);
        return $exp[count($exp) - 1] == "html";
    }

    /**
     * That function return the parsed values of the HTML file content. 
     *
     * Beware it have a lot of IFs :'(
     * @throws NotLoadedFile If the class don't haves a file loaded.
     * @return string|null
     */
    final public function parseFile(){
        if(!$this->got_document) throw new NotLoadedFile("There's no HTML document loaded!", 1);
        $rt_str = $this->content;
        $maped_arr = array(
            "%message%" => $this->error_message,
            "%file%" => is_null($this->file_throwed) ? "[Anonymous file]" : $this->file_throwed,
            "%line%" => is_null($this->line_error) ? "[Anonymous Line]" : $this->line_error,
            "%title%" => "Error Unexpected!",
            "%btn_rt%" => $this->btn_rt
        );
        $a = str_replace("%message%", $maped_arr['%message%'], $this->content);
        $b = str_replace("%file%", $maped_arr["%file%"], $a);
        $c = str_replace("%line%", $maped_arr['%line%'], $b);
        $d = str_replace("%title%", $maped_arr['%title%'], $c);
        $rt_str = str_replace("%btn_rt%", $maped_arr['%btn_rt%'], $d);
        return $rt_str;
    }

    /**
     * Starts the class with a document to be parsed
     * @author Giulliano Rossi <giulliano.scatalon.rossi@gmail.com>
     *  ************************************************************
     * @param string $documnetHTML The HTML file to connect and parse.
     * @param string $error_message The error string message.
     * @param string|null $file_throwed The file that throwed the exception
     * @param string|null $btn_rt_lc The button to return to the previous page. 
     * @param int|null $line_error The line that showed the error
     */
    final public function __construct(string $documentHTML, string $error_message, string $file_throwed = null, int $line_error = null, string $btn_rt_lc){
        if($this->got_document) throw new AlreadyLoadedFile("The class already have a document loaded", 1);
        if(!$this->checkFileValid($documentHTML)) throw new InvalidFileType("The file '$documentHTML' is not valid!", 1);
        $this->page_templated = $documentHTML;
        $this->file_throwed = $file_throwed;
        $this->lin_error = $line_error;
        $this->btn_rt = $btn_rt_lc;
        $this->error_message = $error_message;
        $this->content = file_get_contents($documentHTML);
        $this->got_document = true;
    }
}
?>