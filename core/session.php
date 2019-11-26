<?php
namespace SessionSystem;
require_once "/var/www/html/lpgp-server/core/Exceptions.php";

use SessionSystemExceptions\AlreadyLoggedUserError;
use SessionSystemExceptions\NoSessionStarted;
use SessionSystemExceptions\NoUserLogged;
use SessionSystemExceptions\SessionAlreadyRunning;

class SessionSystem{
    /**
     * That class contains the main actions with the account system.
     * The accounts are storaged in $_SESSION. 
     * That class haves the main connection with the reserved keys on the $_SESSION.
     * @var string|null $user The actual user logged, if there's no user logged in will be null
     * @var bool $logged_user If there's a user logged on the session.
     * @var string|null $mode The type of user that's logged, it can be 'prop'  for proprietaries, 'normie' for normal users, null if there's no user logged.
     * @var bool $started_session If there's a session started.
     * 
     * @const string NAME_KEY The key on $_SESSION that storages the username logged.
     * @const string MODE_KEY The key on $_SESSION that storages the mode of the user logged.
     * @const string LOGGED_KEY The key on $_SESSION that storages if there's a user logged.
     * @const string DTF_NAME The default value for the username key at $_SESSION if there's no user logged.
     * @const string DTF_MODE The default value for the mode key at $_SESSION if there's no user logged.
     * @const array VLS_LOGGED The default values for the logged key at $_SESSION .
     */
    private $user;
    private $logged_user;
    private $mode;
    private $started_session;

    const NAME_KEY = "username";
    const MODE_KEY = "mode";
    const LOGGED_KEY = "logged";
    const DTF_NAME = "none";
    const DTF_MODE = "none";
    const VLS_LOGGED = ['true', 'false'];

    public function parseVars(){
        /**
         * Sets the vars on the class. 
         * Thoose vars are connected to keys on the $_SESSION.
         * @throws NoSessionStarted If there's no session started;
         * @return void
         */
        if(!$this->started_session || !isset($_SESSION[self::LOGGED_KEY])) throw new NoSessionStarted("There's no session started!", 1);
        $this->logged_user = $_SESSION[self::LOGGED_KEY] == self::VLS_LOGGED[0];
        if($_SESSION[self::LOGGED_KEY] == self::VLS_LOGGED[0]){  // the true value;
            $this->user = $_SESSION[self::NAME_KEY];
            $this->mode = $_SESSION[self::MODE_KEY];
        }
        else{
            $this->user = null;
            $this->mode = null;
        }
    }

    public function setDefaultValues(){
        /**
         * Sets the keys at the $_SESSION.
         * @throws NoSessionStarted If there's no session started yet
         * @return void
         */
        if(!$this->started_session || !isset($_SESSION)) throw new NoSessionStarted("There's no session started yet!", 1);
        $_SESSION[self::LOGGED_KEY] = self::VLS_LOGGED[1];
        $_SESSION[self::NAME_KEY] = self::DTF_NAME;
        $_SESSION[self::MODE_KEY] = self::DTF_MODE;
        $this->parseVars();
    }

    public function __construct(){
        /**
         * Starts the class and the vars at the $_SESSION.
         * @throws SessionAlreadyRunning If there's a session started already
         */
        if($this->started_session || isset($_SESSION[self::LOGGED_KEY])) throw new SessionAlreadyRunning("There's a session running already!", 1);
        session_start();
        $this->started_session = true;
        $this->setDefaultValues();
    }

    public function __destruct(){
        /**
         * Destructs the class and ends the session.
         * @throws NoSessionStarted If there's no session started already.
         * @return void
         */
        if(!$this->started_session || !isset($_SESSION[self::LOGGED_KEY])) throw new NoSessionStarted("There's no session started!", 1);
        session_unset();
        session_destroy();
        $this->user = null;
        $this->mode = null;
        $this->logged_user = false;
        $this->started_session = false;
    }

    public function setVlsToSession(){
        /**
         * Sets the values at the attributtes to the $_SESSION.
         * @throws NoSessionStarted If there's no session started in the system.
         * @return void
         */
        if(!$this->started_session || !isset($_SESSION[self::LOGGED_KEY])) throw new NoSessionStarted("There's no session started!", 1);
        $_SESSION[self::NAME_KEY] = is_null($this->user) ? self::DTF_NAME : $this->user;
        $_SESSION[self::MODE_KEY] = is_null($this->mode) ? self::DTF_MODE : $this->mode;
        $_SESSION[self::LOGGED_KEY] = $this->logged_user ? self::VLS_LOGGED[0] : self::VLS_LOGGED[1];
    }

    public function setLoginData(string $username, string $mode){
        /**
         * Sets a user as logged in the session.
         * @throws NoSessionStarted If there's no session started yet.
         * @throws AlreadyLoggedUserError If there's a user logged at the session already.
         * @param string $username The name of the user that made the login.
         * @param string $mode The type of the user that made the login.
         * @return void.   Procedure
         */
        if(!$this->started_session || !isset($_SESSION[self::LOGGED_KEY])) throw new NoSessionStarted("There's no session started!", 1);
        if($this->logged_user) throw new AlreadyLoggedUserError("There's a user logged already!", 1);
        $this->user = $username;
        $this->mode = $mode;
        $this->logged_user = true;
        $this->setVlsToSession();
    }

    public function unsetLoginData(){
        /**
         * Unsets the login information from a logged user on the system. Works with the logoff
         * @throws NoSessionStarted If there's no session started
         * @throws NoUserLogged If there's no user logged
         * @return void
         */
        if(!$this->started_session || !isset($_SESSION[self::LOGGED_KEY])) throw new NoSessionStarted("There's no session started ", 1);
        if(!$this->logged_user) throw new NoUserLogged("There's no user logged already!", 1);
        $this->user = null;
        $this->logged_user = false;
        $this->mode = null;
        $this->setVlsToSession();
    }
}
?>