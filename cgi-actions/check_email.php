<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/js-handler.php";

use Core\ProprietariesData;
use Core\UsersData;

if($_POST['btn-resend']){
    if($_SESSION['mode'] == "normie"){
        $usr_obj = new UsersData("giulliano_php", "");
        $usr_obj->sendCheckEmail($_SESSION['user']);
    }
    else{

    }
}
?>