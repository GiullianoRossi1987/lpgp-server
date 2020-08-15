<?php
require_once "core/control/controllers.php";
require_once "core/control/exceptions.php";
require_once "core/adj_manager.php";
use Control\BaseController;
use Control\SignaturesController;
use Control\SignatureReferenceError;
use Control\ClientsController;
use Adj_Core\SQLite3Connector;

$obj = new SQLite3Connector("core/adj_db.db");
echo "Connected";

?>
