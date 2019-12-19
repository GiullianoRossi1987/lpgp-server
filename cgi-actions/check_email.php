<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";

use Core\UsersData;
use Core\ProprietariesData;

if(array_key_exists('btn-resend', $_POST)){
    if($_SESSION['mode'] == "normie"){
        $usr_obj = new UsersData("giulliano_php", "");
        $usr_obj->sendCheckEmail($_SESSION['user']);
    }
    if($_SESSION['mode'] == "proprietary"){
        $prop_obj = new ProprietariesData("giulliano_php", "");
        $prop_obj->sendCheckEmail($_SESSION['user']);
    }
    echo "<h1>Email sended successfully!</h1>\n<button class=\"default-btn btn darkble-btn\" onclick=\"window.location.replace('http://localhost/lpgp-server/cgi-actions/check-email-stp1.php');\">Go back</button>";
}
else if(array_key_exists("btn-code", $_POST)){
    if($_SESSION['mode'] == "normie"){
        $usr = new UsersData("giulliano_php", "");
        if($usr->authUserKey($_SESSION['user'], $_POST['code'])){
            $usr->setUserChecked($_SESSION['user'], true);
            echo "<script>window.location.replace(\"http://localhost/lpgp-server/\");</script>";
        }
        else{
            echo "<script>showError(\"Invalid Code!\");</script>";
            echo "<button class=\"darkble-btn btn default-btn\" onclick=\"window.location.replace('http://localhost/lpgp-server/cgi-actions/check-email-stp1.php');\">Try again!</button>";
        }
    }
    else{
        $prop = new ProprietariesData("giulliano_php", "");
        if($prop->authPropKey($_SESSION['user'], $_POST['code'])){
            $prop->setProprietaryChecked($_SESSION['user'], true);
            echo "<script>window.location.replace(\"http://localhost/lpgp-server\");</script>";
        }
        else{
            echo "<script>showError(\"Invalid Code\");</script>";
            echo "<button class=\"darkble-btn btn default-btn\" onclick=\"window.location.replace('http://localhost/lpgp-server/cgi-actions/check-email-stp1.php');\">Return</button>";
        }
    }
}
?>