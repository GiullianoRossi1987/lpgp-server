<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
</head>
<body>
	<canvas id="chart-teste" width="10" height="10">
		<script>
			// test with charts
			var to = document.getElementById("chart-teste").getContext('2d');
			var chart = new Chart(to, {
				type: "bar",
				data:{
					labels: ['teste1', 'teste2'],
					datasets: [{
						label: "fofo",
						backgroundColor: [
                			'rgba(255, 99, 132, 0.2)',
                			'rgba(54, 162, 235, 0.2)',
                			'rgba(255, 206, 86, 0.2)',
                			'rgba(75, 192, 192, 0.2)',
                			'rgba(153, 102, 255, 0.2)',
                			'rgba(255, 159, 64, 0.2)'
            			],
	            		borderColor: [
                			'rgba(255, 99, 132, 1)',
                			'rgba(54, 162, 235, 1)',
                			'rgba(255, 206, 86, 1)',
                			'rgba(75, 192, 192, 1)',
                			'rgba(153, 102, 255, 1)',
                			'rgba(255, 159, 64, 1)'
        	    		],
						borderWidth: 1,
						data: [34, 34]
					}]
				},
				options:{
					scales:{
						yAxes: [{
							ticks: {
								BeginAtZero: true
							}
						}]
					}
				}
			}
			);
		</script>
	</canvas>
</body>
</html>