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
	<canvas id="client-chart" width="500" height="500"></canvas>
	<?php
		$acc->getClientSuccessful(10, true);
		echo $acc->generateChart("client-chart");
	?>
</body>
</html>