<?php
if(session_status() == PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/core/Core.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/core/js-handler.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/core/Exceptions.php";

use Core\ClientsData;
use ClientsExceptions\AuthenticationError;
use function JSHandler\sendUserLogged;
use function JSHandler\createClientAuthCard;

sendUserLogged();  // preventing bugs

move_uploaded_file($_FILES['to-check']['tmp_name'][0], U_CLIENTS_CONF . $_FILES['to-check']['name'][0]);

$obj = new ClientsData("giulliano_php", "");
$mainData = $obj->getClientAuthData(U_CLIENTS_CONF . $_FILES['to-check']['name'][0]);
$brute = $mainData['brute'];
$bruteDataCon = '<div class="brutedata">' . "\n<ul>\n";
$bruteDataCon .= '<li><b>Client</b> ' . $brute['Client'] . '</li>' . "\n";
$bruteDataCon .= '<li><b>Date Creation</b>: ' . $brute['Dt'] . '</li>' . "\n";
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>LPGP - Client</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/new-layout.css">
    <script src="../js/main-script.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
    <link rel="shortcut icon" href="../media/new-logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
    <script src="../js/actions.js"></script>
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
</head>
<body>
	<script>
        var show = false;
        $(document).ready(function(){
            setAccountOpts(true);
            setSignatureOpts();
            applyToA();
            applyToForms();
            $("#img-user").css("background-image", "url(" + getLinkedUserIcon() + ")");
			if(show){
				$("#modal-done").modal('show');
				show = false;
			}
        });

        $(document).ready(function(){
            applyToA();
		});

		$(document).on("change", ".al", function(){
			$("#go").removeClass("disabled");
		});

    </script>
	<?php
	if(isset($_GET['alert'])){
		echo "<script>show=true</script>";
	}
	?>
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
	<div class="container-fluid container content-container" style="margin-top: 10%;">
		<div class="row main-row">
			<div class="col-12 content" style="position: relative">
				<center>
					<?php
						if($mainData['valid']){
                            echo '<i class="fas fa-check" style="color: green; font-size: 150px"></i>';
                            echo '<br>';
                            echo '<h1 style="color: green">Hurray! The client is valid!</h1>';
                            // client card
                            echo createClientAuthCard($mainData['soft']);
                        }
						else {
							echo '<i class="fas fa-times" style="color: red; font-size: 150px"></i><br>';
							echo '<small style="color: red">' . $mainData['error'] . '</small>';
						}
					?>
				</center>
				<br>
				<a role="button" class="btn btn-lg btn-secondary" data-toggle="collapse" href="#bruteCollapse" aria-expanded="false" aria-controls="bruteCollapse">
					Show brute data
				</a>
				<div class="collapse" id="bruteCollapse">
					<?php echo $bruteDataCon; ?>
				</div>
			</div>
		</div>
	</div>
	<div class="footer-container container" style="max-width: 100% !important; position: relative; margin-left: 0;">
        <div class="footer-row row">
            <div class="footer col-12" style="height: 150px; background-color: black; margin-top: 100%; position: relative; max-width: 100% !important; margin-left: 0;">
                <div class="social-options-grp">
                    <div class="social-option">
                        <a href="https://github.com/GiullianoRossi1987" target="_blanck" id="github" class="social-option-footer">
                        <span><i class="fab fa-github"></i></span></a>
                    </div>
                    <div class="social-option-footer">
                        <a href="https://" target='_blanck' id="facebook">

                        </a>
                    </div>
                    <div class="social-option-footer">
                        <a href="https://" target='_blanck' id="twitter"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
