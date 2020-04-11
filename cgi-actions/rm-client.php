<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";

use Core\ClientsData;

if(isset($_GET['client'])){
	$cl_id = base64_decode($_GET['client']);
	$obj = new ClientsData("giulliano_php", "");
	$obj->rmClient($cl_id);

	header("Location: ch-client.php?client=" . $_GET['client']);
	
}