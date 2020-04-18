<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/devcenter/devcore/thread-system.php";

use ThrearedServer\ThrearedServer;

$server = new ThrearedServer();
$server->loop();
?>