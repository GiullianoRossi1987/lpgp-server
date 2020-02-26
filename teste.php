<?php

use ClientsDatabase\ClientsManager;

require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/devcenter/devcore/dbapi.php";

$db = new ClientsManager("giulliano_php", "");

$db->genAuth("teste", 'signatures.d');
echo "Done";
?>