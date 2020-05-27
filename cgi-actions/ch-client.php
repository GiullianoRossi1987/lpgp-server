<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/core/Core.php";

use Core\ClientsData;

if(isset($_GET['client'])){
	$cl_id = base64_decode($_GET['client']);
	$obj = new ClientsData("giulliano_php", "");
	$cl_dt = $obj->getClientData((int)$cl_id);

	$name_ip = '<input type="text" name="client-name" id="cl-nm" class="form-control al" value="' . $cl_dt['nm_client'] .'">';
	$tk_ip = '<input type="text" name="client-tk" id="cl-tk" class="form-control al" value="' . $cl_dt['tk_client'] . '" readonly>';
	$sel = '<select class="form-control al" id="permissions-sel" name="permissions">';
	if($cl_dt['vl_root'] == 1){
        $opts = '<option value="1" selected>Root</option>' . '<option value="0">Normal</option>';
    }
    else{
        $opts = '<option value="1">Root</option>' . '<option value="0" selected>Normal</option>';
    }
	$sel .= $opts .  "</select>";
	$id = '<input type="hidden" name="client" value="' . $_GET['client'] . '">';
	$del = '<a class="btn btn-lg btn-danger" role="button" type="button" href="rm-client.php?client=' . $_GET['client'] . '">Delete this client</a>';
	$modalLink = '<a href="client-data.php?client=' . $_GET['client'] .'" role="button" class="btn btn-lg btn-success" type="button">
						Click here to download the new authentication file.</a>';
}
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
				<form action="changing-client.php" method="post">
					<h1>Changing Client configurations</h1>
					<?php echo $id; ?>
					<label for="cl-nm" class="form-label">
						The client name
					</label>
					<?php echo $name_ip; ?>
					<br>
					<label for="permissions-sel" class="form-label">
						Client Permissions Type
					</label>
					<br>
					<?php echo $sel; ?>
					<br>
					<button type="button" class="btn btn-lg btn-secondary" data-toggle="collapse" aria-expanded="false" aria-controls="tk-dv" data-target="#tk-dv">
						See the raw token
					</button>
                    <br>
					<div class="collapse" id="tk-dv">
						<br>
						<div class="input-group">
							<?php echo $tk_ip; ?>
							<br>
							<!-- Button trigger modal -->
							<a type="button" class="btn btn-primary" data-toggle="modal" data-target="#modelToken" style="color: white;">
								<span>
									<i class="fas fa-plus"></i>
								</span>
								Require a new token
							</a>

							<!-- Modal TOKEN -->
							<div class="modal fade" id="modelToken" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title">Warning</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
										</div>
										<div class="modal-body">
											Changing the client token must have consequences, after doing that, you must download
											the new client authentication file.
											<h1>Are you sure to do that</h1>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
											<button type="submit" class="btn btn-primary" name="chmodal">Yes</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<br>
					<?php echo $del; ?>
					<button type="submit" class="btn btn-lg btn-success disabled" id="go" name="submit">Save changes</button>
					<a href="my_account.php" role="button" type="button" class="btn btn-lg btn-secondary">Cancel</a>
					<!-- Modal Saved Changes -->
					<div class="modal fade" id="modal-done" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">Saved changes</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
								</div>
								<div class="modal-body">
									Your client changes were saved successfully!
									<?php echo $modalLink; ?>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-success" data-dismiss="modal">Ok</button>
								</div>
							</div>
						</div>
					</div>
				</form>
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
