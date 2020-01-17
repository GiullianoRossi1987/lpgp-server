<?php
if(session_status() == PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/js-handler.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Exceptions.php";


use Core\SignaturesData;
use function JSHandler\sendUserLogged;
use Core\PropCheckHistory;
use Core\UsersCheckHistory;


sendUserLogged();   // Just preventing any error in the localStorage.
$signature_img = "<img src=\"%path%\" alt=\"%alt%\">";
$signature_msg = "";

// uploads the file
move_uploaded_file($_FILES['signature-ext']['tmp_name'][0], $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/usignatures.d/" . $_FILES['signature-ext']['name'][0]);

die("testing");

if($_SESSION['mode'] == 'prop'){
	$prp_c = new PropCheckHistory("giulliano_php", "");
	$sig = new SignaturesData("giulliano_php", "");
	try{
		if($sig->checkSignatureFile($_FILES['signature-ext']['name'])){
			$rp = str_replace("%path%", "src1", $signature_img);
			$rp1 = str_replace("%alt%", "valid signature", $rp);
			$signature_img = $rp1;
			unset($rp);
			unset($rp1);
			$signature_msg = "The signature is valid!";
		}
	}
	catch(Exception $e){ // TODO finish

	}
}
?>