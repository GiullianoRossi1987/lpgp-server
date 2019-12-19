<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";
use templateSystem\ErrorTemplate;
session_start();

if($_SESSION['user_logged'] != "true" || !isset($_SESSION['user_logged'])){
    $err = new ErrorTemplate($_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/templates/500-internal-error.html", "There's no user logged!", __FILE__, 7, "<button class=\"default-btn btn darkble-btn\" onclick=\"window.location.replace('http://localhost/lpgp-server/')\">Back to the index</button>");
    die($err->parseFile());
}
else{
    session_unset();
    echo "<script>window.location.replace('http://localhost/lpgp-server/');</script>";
}
?>