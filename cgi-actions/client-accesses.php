<?php
if(session_status() == PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/core/Core.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/core/js-handler.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/core/charts.php";

use function JSHandler\sendUserLogged;
use Core\ClientsData;
use Core\ClientsAccessData;
use Charts_Plots\AccessPlot;
use const LPGP_CONF;

sendUserLogged();  // preventing bugs
$objClients = new AccessPlot("Client ");

if(isset($_GET['client'])){
	$client = (int)base64_decode($_GET['client']);
	$objClients->getClientAccesses($client, true);
}
else if(isset($_POST['changer'])){
    if($_POST['client'] == 0){
        // all clients
        if($_POST['mode'] == 0) $objClients->allClientsChart($_SESSION['user'], true);
        else if($_POST['mode'] == 1) $objClients->allClientsSuccessfulChart($_SESSION['user'], true);
        else $objClients->allClientsUnsuccessulChart($_SESSION['user'], true);
    }
    else{
        // specific clients
        if($_POST['mode'] == 0) $objClients->getClientAccesses($_POST['client'], true);
        else if($_POST['mode'] == 1) $objClients->getClientSuccessful($_POST['client'], true);
        else $objClients->getClientUnsuccessful($_POST['client'], true);
    }
}

$clients = new ClientsData(LPGP_CONF['mysql']['sysuser'], LPGP_CONF['mysql']['passwd']);
$all = $clients->getClientsByOwner($_SESSION['user']);
$clls = "";

foreach($all as $clientData) $clls .= '<option value="' . $clientData['cd_client'] . '">' . $clientData['nm_client'] . '</option>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LPGP Oficial Server</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="../jquery/lib/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../css/new-layout.css">
    <script src="../js/main-script.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
    <link rel="shortcut icon" href="../media/new-logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
    <script src="../js/actions.js"></script>
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
	<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
	<script src="../js/charts.js"></script>
	<script src="../jquery/lib/jquery-3.4.1.min.js" charset="utf-8"></script>
	<script src="../jquery/lib/bootstrap/js/bootstrap.js" charset="utf-8"></script>
	<script src="../js/requester.js" charset="utf-8"></script>

</head>
<style>
</style>
<body>
    <script>
        $(document).ready(function(){
            setAccountOpts(true);
            setSignatureOpts();
            applyToA();
            applyToForms();
            $("#img-user").css("background-image", "url(" + getLinkedUserIcon() + ")");
        });

        $(document).ready(function(){
            applyToA();
            $("#chart-controls").collapse('show');
        });

		$(document).on("click", "#changer", function(){
			requestChart($("#client-selector").val(), $("#mode-selector").val(), "chart-dispose");
		});
    </script>
    <div class="container-fluid header-container" role="banner" style="position: fixed;">
        <div class="col-12 header" style="height: 71px; transition: background-color 200ms linear;">
            <div class="opt-dropdown dropdown login-dropdown">
                    <button type="button" class="btn btn-lg default-btn-header dropdown-toggle" data-toggle="dropdown" id="account-opts" aria-haspopup="true" aria-expanded="false">
                        <span class="nm-tmp">Account</span>
                    </button>
                    <div class="dropdown-menu opts" aria-labelledby="account-opts"></div>
                </div>
                <div class="opt-dropdown dropdown after-opt signatures-dropdown">
                    <button class="dropdown-toggle btn btn-lg default-btn-header" data-toggle="dropdown" aria-expanded="false" aria-haspopup="true" id="signature-opts">
                        Signatures
                    </button>
                    <div class="dropdown-menu opts" aria-labelledby="signature-opts"></div>
                </div>
                <div class="opt-dropdown dropdown after-opt help-dropdown">
                    <button class="dropdown-toggle btn btn-lg default-btn-header" data-toggle="dropdown" aria-expanded="false" aria-haspopup="true" id="help-opt">
                        Help
                    </button>
                    <div class="dropdown-menu opts" aria-labelledby="help-opt">
                        <a href="http://localhost/docs/" class="dropdown-item">Documentation</a>
                        <a href="http://localhost/about.html" class="dropdown-item">About Us</a>
                        <a href="http://localhost/contact-us.html" class="dropdown-item">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <hr>
    <div class="container-fluid container-content" style="position: relative; margin-top: 5%;">
        <div class="chart-row row">
			<div class="controls-container container container-fluid">
				<div class="row controls-row">
					<div class="chart-controls col-md-12 col-sm-12 collapse" id="chart-controls" style="max-width: 100% !important;">
						<form method="post">
                            <center><h1>Chart Options</h1></center>
							<label for="client-selector" class="form-label">
								<h2>Choose the client</h2>
							</label>
							<select name="client" id="client-selector" class="form-control">
								<option value="0">All</option>
								<?php echo $clls;?>
							</select>
							<br>
							<label for="mode-selector" class="form-label">
								<h3>Choose the aditional options</h3>
							</label>
							<select name="mode" id="mode-selector" class="form-control">
								<option value="0">All the accesses</option>
                                <option value="1">Only the successful</option>
                                <option value="2">Only the unsuccessful</option>
							</select>
                            <br>
                            <button type="button" class="btn btn-primary" name="changer" id="changer">Load chart</button>
						</form>
					</div>
				</div>
				<div class="toggler-row row">
					<a href="#chart-controls" role="button" class="btn btn-block btn-dark" data-toggle="collapse" aria-expanded="true" aria-controls="chart-controls">
						<span>
							<i class="fas fa-caret-down"></i>
						</span>
					</a>
				</div>
			</div>
            <br>
			<div class="chart-container container container-fluid" style="max-width: 100% !important; margin-top: 5%;">
				<div class="chart-row row">
					<div class="col-md-12 col-sm-12 chart-col" id="chart-dispose">
						<canvas id="clients-plot" width="500" height="500"></canvas>
						<!-- <?php
							// echo $objClients->generateChart("clients-plot");
						?> -->
					</div>
				</div>
			</div>
        </div>
    </div>
    <br>
    <div class="footer-container container" style="max-width: 100% !important; position: relative; margin-left: 0;">
        <div class="footer-row row">
            <div class="footer col-12" style="height: 150px; background-color: black; margin-top: 100%; position: relative; max-width: 100% !important; margin-left: 0;">
                <div class="social-options-grp">
                    <div class="social-option">
                        <a href="https://github.com/GiullianoRossi1987" target="_blanck" id="github" class="social-option-footer">
                            <span><i class="fab fa-github"></i></span>
                            Visit our github!
                        </a>
                    </div>
                    <br>
                    <div class="social-option-footer">
                        <a href="https://" target='_blanck' id="facebook">
                            <span><i class="fab fa-facebook"></i></span>
                            Visit our facebook!
                        </a>
                    </div>
                    <br>
                    <div class="social-option-footer">
                        <a href="https://" target='_blanck' id="twitter"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
