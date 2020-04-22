<?php
if(session_status() == PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/js-handler.php";

use function JSHandler\sendUserLogged;
use Core\ClientsData;
use Core\ClientsAccessData;

$clientObj = new ClientsData("giulliano_php", "");
$clientAccess = new ClientsAccessData("giulliano_php", "");