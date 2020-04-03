<?php
require_once "core/Core.php";

use Core\ClientsData;
$clients = new ClientsData("giulliano_php", "");

echo $clients->genConfigClient(1);
?>