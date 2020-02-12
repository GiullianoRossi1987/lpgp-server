<?php
require_once "./devcenter/devcore/dbapi.php";

use Database\ServersManager;

$t = new ServersManager("giulliano_php", "");
echo $t->ckIP("198.178.0.0") ? "foi" : "nao foi";

?>
