<?php

namespace SessionSystem;
// in error case change for your DocumentRoot
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Exceptions.php";

class SessionSystem{
    /**
     * That class contains the main actions for the session of a user at the website.
     * Here goes the methods thoose does the login, logoff, returns the logged user data, starts and ends a session 
     * @var string $logged_user The user that's logged on the site actualy, if there's no user logged the default value is 'none'
     * @var bool $logged_now If there's a user logged on the site right now.
     * @var string $mode The mode the type of the user wich is logged right now, it can be only:
     *                   * 'Prop' => For proprietaries users.
     *                   * 'Normie' => For normal users.
     *                   * 'undefined' => if there's no user logged now.
     * @var bool|null $checked If the logged user haves the email checked, that will usefull for database actions. It only can be null if there's no user logged now.
     * @var bool $started_session If the session has started
     * @const NAME_KEY the key from $_SESSION wich contains the name of the logged user.
     * @const MODE_KEY the key from $_SESSION wich contains the mode of the logged user.
     * @const LOGGED_KEY the key from $_SESSION wich contains if the there's a user  logged.
     * @const CHECKED_KEY the key from $_SESSION wich contains if the logged user haves the email logged or not
     */
    private $logged_user;
    private $logged_now;
    private $mode;
    private $checked;
    private $started_session;

    const NAME_KEY   = "name_usr";
    const MODE_KEY   = "mode_usr";
    const LOGGED_KEY = "logged_usr";

    public function parseDataKeys(){
        /**
         * Sets the class attributtes wich are connected to a $_SESSION key.
         * @throws NoSessionStarted if there's no session started
         * @return void
         */
        if(!$this->started_session) throw new NoSessionStarted("There's no session started!", 1);
        
    }
}

throw new NoSessionStarted("Teste", 1);
?>