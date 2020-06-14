<?php
require_once "core/control/controllers.php";
require_once "core/control/exceptions.php";
use Control\BaseController;
use Control\SignaturesController;
use Control\SignatureReferenceError;

$obj = new SignaturesController("core/control/control.json");
try{
    $obj->addUploadRecord(1, true);
}
catch(SignatureReferenceError $e){ echo "Signature error";}
echo "ok";
?>
