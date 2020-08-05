<?php
if(session_status() == PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/core/Core.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/core/js-handler.php";

use Core\UsersData;
use Core\ProprietariesData;
use Core\SignaturesData;
use Core\ClientsData;
use function JSHandler\genResultNormalUser;
use function JSHandler\genResultProprietary;
use function JSHandler\genResultClient;

function queryAll(string $needle): array{
    // simple handle
    return [];  // tmp
}

function queryUsrs(string $needle): array{
    // simple handle
    return []; // tmp
}

if(isset($_POST['scope']) && isset($_POST['mode']) && isset($_POST['needle'])){
    // starts the query's
    $blank_ = [];
    $content = "";
    if($_POST['scope'] == "all"){
        switch((int)$_POST['mode']){
            case 0:
                $blank_ = queryAll($_POST['needle']);
                break;
            case 1:
                $blank_ = queryUsrs($_POST['needle']);
                break;
            case 2:
                $nrl = new UsersData(LPGP_CONF['mysql']['sysuser'], LPGP_CONF['mysql']['passwd']);
                $blank_ = $nrl->qrUserByName($_POST['needle'], false);
                foreach($blank_ as $item) $content .= genResultNormalUser($item);
                unset($nrl);
                die($content);
                break;
            case 3:
                $prp = new ProprietariesData(LPGP_CONF['mysql']['sysuser'], LPGP_CONF['mysql']['passwd']);
                $blank_ = $prp->qrPropByName($_POST['needle'], false);
                foreach($blank_ as $data) $content .= genResultProprietary($data);
                unset($prp);
                die($content);
                break;
            case 4:
                $cld = new ClientsData(LPGP_CONF['mysql']['sysuser'], LPGP_CONF['mysql']['passwd']);
                $blank_ = $cld->qrAllClients($_POST['needle']);
                foreach($blank_ as $item) $content .= genResultClient($item);
                unset($cld);
                die($content);
                break;
            default: die("INTERNAL ERROR");
        }
    }
    else{
        switch((int)$_POST['mode']){
            case 0:
                $blank_ = queryAll($_POST['needle']);
                break;
            case 4:
                $cld = new ClientsData(LPGP_CONF['mysql']['sysuser'], LPGP_CONF['mysql']['passwd']);
                $blank_ = $cld->qrClientsOfProp($_POST['needle'], $_SESSION['user']);

                unset($cld);
                break;
            default: die('INTERNAL ERROR');
        }
    }
    echo count($blank_) > 0 ? var_dump($blank_) : "No Results";
}
 ?>
