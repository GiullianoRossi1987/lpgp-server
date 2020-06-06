<?php
namespace Control{
    require_once $_SERVER['DOCUMENT_ROOT'] . "/core/Core.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/core/Exceptions.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/core/control/exceptions.php";

    use Core\SignaturesData;
    use Core\UsersData;
    use Core\ProprietariesData;
    use Core\ClientsData;

    /**
     * That class have the basic operations and structures to all the control files,
     * it works only reading, loading, dumping and writing a control file. The
     * control files are simple JSON records files, they're used to log specific
     * actions at the main server. It's important to the new security feature, 
     */
    class BaseController{

    }
}
