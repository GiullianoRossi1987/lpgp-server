<?php
namespace Controllers{
    require_once $_SERVER['DOCUMENT_ROOT'] . "/core/Core.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/core/Exceptions.php";

    use Exception;
    use const LPGP_CONF;
    use Core\UsersData;
    use Core\ClientsData;
    use Core\ProprietariesData;
    use Core\SignaturesData;


    /**
     * <Exception> Thrown when a controller try to load a control file, but it
     * already haves another control file loaded. It exists to prevent that a control
     * file data stay as it was the data from another control file.
     *
     */
    class ControlFileLoaded extends Exception{

        public function __construct(){
            parent::__construct("There's a control file loaded already");
        }
    }

    /**
     * <Exception> Thrown when a controller try to achieve a control file that
     * isn't loaded by him.
     */
    class ControlFileNotFound extends Exception{

        public function __construct(){
            parent::__construct("There's no control file loaded yet!");
        }
    }

    /**
     * <Exception> Thrown when the controller's trying to load a control file, but
     * the controller can't access it, maybe a PermissionError or a FileNotFoundError.
     */
    class ControlFileUnreachable extends Exception{

        public function __construct(string $controlPath){
            parent::__construct("Can't access the $controlPath file");
        }
    }

    /**
     * That class have the basic operations of a controller. it's used as the super
     * class of the other specific controllers, it's the superclass of the following
     * hierarchy. If you want to make your own controller, then extend your class
     * from it. To that basic structure you must set the attributes in this class,
     * as I said, they're essential to the basic operations of any controller.
     *
     * @param string|null $control The control file path.
     * @param boolean $gotData If the class got the control file and it data, that attribute
     *                          must be true. But, if the conrol file don't have the control
     *                          it must be false.
     *
     */
    class SimpleController{

    }
}
