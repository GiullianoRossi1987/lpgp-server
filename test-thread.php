<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/js-handler.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/charts.php";

use Core\ClientsAccessData;
use Charts_Plots\AccessPlot;
use function JSHandler\createAccessChart;

$acc = new AccessPlot("teste");

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
	<script src="js/main-script.js"></script>
	<script src="js/charts.js"></script>
</head> 
<body>
	<canvas id="client-chart" width="100" height="100"></canvas>
	<?php
		$acc->allClientsChart("giulliano_teste", true);
		echo $acc->generateChart("client-chart");
		$acc->dieBase();
	?>
</body>
</html>