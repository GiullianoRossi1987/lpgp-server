<?php
if(session_status() == PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/core/Core.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/core/js-handler.php";

use Core\UsersData;
use Core\ProprietariesData;
use Core\SignaturesData;
use Core\ClientsData;

die(var_dump($_POST));
 ?>
