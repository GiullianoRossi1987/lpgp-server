<?php
require_once "core/control/controllers.php";
require_once "core/control/exceptions.php";
use Control\BaseController;
use Control\SignaturesController;
use Control\SignatureReferenceError;

$obj = new SignaturesController("core/control/control.json");
echo $obj->generateDownloadToken();
echo "\nok";
?>
