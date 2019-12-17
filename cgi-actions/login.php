<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/js-handler.php";

use Core\ProprietariesData;
use Core\UsersData;
use function JSHandler\sendUserLogged; 

if($_POST['account-type'] == "normal"){
    $user_obj = new UsersData("giulliano_php", "");  // trade for your username and password at MySQL
    $auth = $user_obj->login($_POST['user-name'], $_POST['password-input']);
    sendUserLogged();
}
else if($_POST['account-type'] == "proprietary"){
    $prop_obj = new ProprietariesData("giulliano_php", "");
    $prop_obj->login($_POST['user-name'], $_POST['password-input']);
    sendUserLogged();
}
if($_SESSION['checked'] == "false") echo "<script>window.location.replace(\"http://localhost/lpgp-server/cgi-actions/check-email-stp1.php\");</script>";
else echo "<script>window.location.replace(\"http://localhost/lpgp-server/index.html\");</script>";
?>