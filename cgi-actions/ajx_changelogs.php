<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/Core.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/Exceptions.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/core/changelog-core.php";

use Core\ClientsChangeLogs;
use Core\SignaturesChangeLogs;

if(isset($_POST["signature"])){

}
else if(isset($_POST["client"])){

}
else die("ERROR");
 ?>
