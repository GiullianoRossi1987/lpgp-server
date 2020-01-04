<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";
// require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/js-handler.php";

use Core\ProprietariesData;
use Core\UsersData;

if($_POST['account-mode'] == "normal"){
    // setting up the image to the server
    if(isset($_FILES)){
        move_uploaded_file($_FILES['img-user']['tmp_name'][0], $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/u.images/" . $_FILES['img-user']['name'][0]);
    }
    $usr_obj = new UsersData("giulliano_php", "");
    $usr_obj->addUser($_POST['username'], $_POST['password1'], $_POST['email'], true,$_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/u.images/" . $_FILES['img-user']['name'][0]);
    $usr_obj->sendCheckEmail($_POST['username']);
}
else if($_POST['account-mode'] == "proprietary"){
    if(isset($_FILES)){
        move_uploaded_file($_FILES['img-user']['tmp_name'][0], $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/u.images/" . $_FILES['img-user']['name'][0]);
    }
    $prop_obj = new ProprietariesData("giulliano_php", "");
    $prop_obj->addProprietary($_POST['username'], $_POST['password1'], $_POST['email'], true, $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/u.images/" . $_FILES['img-user']['name'][0]);
    $prop_obj->sendCheckEmail($_POST['username']);
}
?>