<?php
if(session_status() == PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/core/Core.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/core/charts.php";

use Core\ProprietariesData;
use Charts_Plots\AccessPlot;
use const Core\LPGP_CONF;

$content = "";

if(isset($_POST['client'])){
    $charter = new AccessPlot("Client: " . $_POST['client']);
    if($_POST['mode'] === 0) $charter->getClientAccesses($_POST['client'], true);
    else if($_POST['mode'] === 1) $charter->getClientSuccessful($_POST['client'], true);
    else $charter->getClientUnsuccessful($_POST['client'], true);
    $content = $charter->generateChart();
}
else{
    $charter = new AccessPlot("Clients of " . $_SESSION['user']);
    if($_POST['mode'] === 0) $charter->getAllClientsChart($_SESSION['user'], true);
    else if($_POST['mode'] === 1) $charter->getAllSuccessfulChart($_SESSION['user'], true);
    else $charter->getAllUnsuccesfulChart($_SESSION['user'], true);
    $content = $charter->generateChart();
}
die($content);
 ?>
