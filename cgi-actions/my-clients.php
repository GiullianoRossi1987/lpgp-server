<?php
if(session_status() == PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/js-handler.php";

use function JSHandler\sendUserLogged;
use function JSHandler\createClientCard;
use Core\ClientsData;

sendUserLogged();  // preventing bugs

$obj = new ClientsData("giulliano_php", "");
$clients = $obj->getClientsByOwner($_SESSION['user']);

