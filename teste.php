<?php
require_once "./devcenter/devcore/dbapi.php";

use Database\ServersManager;

$t = new ServersManager("giulliano_php", "");

$t->sendSMPTTK("server1");

?>
