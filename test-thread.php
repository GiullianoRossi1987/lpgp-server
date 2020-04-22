<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/js-handler.php";
use function JSHandler\createAccessChart;


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
</head>
<body>
	<canvas id="client-plot" width="4" height="4"></canvas>
	<?php
		echo createAccessChart(1, "client-plot");
	?>
</body>
</html>