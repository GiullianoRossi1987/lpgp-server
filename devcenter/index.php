<?php 
require_once "devcore/dbapi.php";
require_once "../core/js-handler.php";

use function JSHandler\sendUserLogged;

sendUserLogged();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>LPGP Devcenter</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.2/popper.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="../js/main-script.js"></script>
	<link rel="stylesheet" href="../css/new-layout.css">
	<link rel="shortcut icon" href="../media/new-logo.png" type="image/x-icon">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>
<body>

	<script>
		$(document).ready(function(){
			setAccountOpts();
			setSignatureOpts();
		});
	</script>

	<div class="container-fluid header-container" role="banner" style="position: relative;">
        <div class="col-md-12 header col-sm-12" style="height: 71px;">
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
                    <a href="./docs/index.php" class="dropdown-item">Documentation</a>
                    <a href="./about.html" class="dropdown-item">About Us</a>
                    <a href="./contact-us.html" class="dropdown-item">Contact Us</a>
                </div>
            </div>
            <br>
        </div>
    </div>
	<div class="container content-container">
		<div class="row-main row">
			<div class="col-md-12 col-sm-12 content">
			</div>
		</div>
	</div>
	<div class="container footer-container">
		<div class="row-main row">
			<div class="col-md-12 col-sm-12 footer">
				<div class="solcial-options-grp">
					<div class="social-option">
						<a href="https://github.com/GiullianoRossi1987/lpgp-server" target="_blank">
							<span>
								<i class="fab fa-github"></i>
							</span>
							Visit our github
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>