<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/devcenter/devcore/dbapi.php";
use ClientsDatabase\ClientsManager;
$cl = new ClientsManager("giulliano_php", "");
$a = $cl->ckPropRef("giulliano");
$b = $cl->ckPropRef(10);  // FALSE
echo $a === false ? "A doesn't exists" : $a;
if($b === false) echo "B doesn't exists";
echo $b;
?>