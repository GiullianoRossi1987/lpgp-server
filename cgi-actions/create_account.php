<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";
// require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/js-handler.php";

use Core\ProprietariesData;
use Core\UsersData;


if($_POST['account-mode'] == "normal"){
    $usr_obj = new UsersData("giulliano_php", "");
    $usr_obj->addUser($_POST['username'], $_POST['password1'], $_POST['email']);
    $usr_obj->sendCheckEmail($_POST['username']);
    $usr_obj->__destruct();
}
else if($_POST['account-mode'] == "proprietary"){
    $prop_obj = new ProprietariesData("giulliano_php", "");
    $prop_obj->addProprietary($_POST['username'], $_POST['password1'], $_POST['email']);
    $prop_obj->sendCheckEmail($_POST['username']);
    $prop_obj->__destruct();
}
?>
