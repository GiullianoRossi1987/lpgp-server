<?php
if(session_status() == PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/js-handler.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Exceptions.php";


use Core\SignaturesData;
use function JSHandler\sendUserLogged;
use function JSHandler\createSignatureCardAuth;
use Core\PropCheckHistory;
use Core\UsersCheckHistory;
use Core\ProprietariesData;
use Core\UsersData;

use SignaturesExceptions\InvalidSignatureFile;
use SignaturesExceptions\SignatureAuthError;
use SignaturesExceptions\SignatureFileNotFound;
use SignaturesExceptions\SignatureNotFound;
use SignaturesExceptions\VersionError;

sendUserLogged();   // Just preventing any error in the localStorage.
$signature_img = "<img src=\"%path%\" alt=\"%alt%\">";
$signature_msg = "";
$prp_obj = new ProprietariesData("giulliano_php", "");
$usr_obj = new UsersData("giulliano_php", "");
$domAdd = "";

// uploads the file
move_uploaded_file($_FILES['signature-ext']['tmp_name'][0], $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/usignatures.d/" . $_FILES['signature-ext']['name'][0]);


if($_SESSION['mode'] == 'prop'){
	$prp_c = new PropCheckHistory("giulliano_php", "");
	$sig = new SignaturesData("giulliano_php", "");
    $prop_id = $prp_obj->getPropID($_SESSION['user']);
	$vl = false;
	try{
		if($sig->checkSignatureFile($_FILES['signature-ext']['name'][0])){
			$data = $sig->getSignatureFData($_FILES['signature-ext']['name'][0]);
			$rp = str_replace("%path%", "src1", $signature_img);
			$rp1 = str_replace("%alt%", "valid signature", $rp);
			$signature_msg = "The signature is valid!";
			$rel_id = $prp_c->addReg($prop_id, $data['ID'], 1, null);
			$vl = true;
		}
	}
	catch(InvalidSignatureFile $e){
		$rel_id = $prp_c->addReg($prop_id, $data['ID'], 0, 1);
		$rp = str_replace("%path%", "src2", $signature_img);
		$rp1 = str_replace("%alt%", "invalid signature", $rp);
		$signature_msg = "The signature is invalid!";
	}
	catch(SignatureNotFound $e){
		$rel_id = $prp_c->addReg($prop_id, $data['ID'], 0, 2);
		$rp = str_replace("%path%", "src2", $signature_img);
		$rp1 = str_replace("%alt%", "invalid signature", $rp);
		$signature_msg = "The signature is invalid!";
	}
	catch(SignatureFileNotFound $e) {
		$rel_id = $prp_c->addReg($prop_id, $data['ID'], 0, 1);
		$rp = str_replace("%path%", "src2", $signature_img);
		$rp1 = str_replace("%alt%", "invalid signature", $rp);
		$signature_msg = "The signature is invalid!";
	}
	catch(SignatureAuthError $e){
		$rel_id = $prp_c->addReg($prop_id, $data['ID'], 0, 3);
		$rp = str_replace("%path%", "src2", $signature_img);
		$rp1 = str_replace("%alt%", "invalid signature", $rp);
		$signature_msg = "The signature is invalid!";
	}
	finally{
		$domAdd .= "<a href=\"https://localhost/lpgp-server/cgi-actions/relatory.php?rel=$rel_id\" role=\"button\" class=\"btn btn-lg btn-primary\">See relatory</a><br><hr>";
		$signature_img = $rp1;
		unset($rp);
		unset($rp1);
		$domAdd .= createSignatureCardAuth($data['ID'], $vl);
	}
}
else{
	$usr_c = new UsersCheckHistory("giulliano_php", "");
	$sig = new SignaturesData("giulliano_php", "");
	$usr_id = $usr_obj->getUserData($_SESSION['user'])['cd_user'];
	$vl = false;
	try{
		if($sig->checkSignatureFile($_FILES['signature-ext']['name'][0])){
			$data = $sig->getSignatureFData($_FILES['signature-ext']['name'][0]);
			$rp = str_replace("%path%", "src1", $signature_img);
			$rp1 = str_replace("%alt%", "valid signature", $rp);
			$signature_msg = "Valid signature";
			$rel_id = $usr_c->addReg($usr_id, $data['ID']);
			$vl = true;
		}
	}
	catch(InvalidSignatureFile $e){
		$rel_id = $usr_c->addReg($usr_id, $data['ID'], 0, 1);
		$rp = str_replace("%path%", "src2", $signature_img);
		$rp1 = str_replace("%alt%", "invalid signature", $rp);
		$signature_msg = "The signature is invalid!";
	}
	catch(SignatureNotFound $e){
		$rel_id = $usr_c->addReg($usr_id, $data['ID'], 0, 2);
		$rp = str_replace("%path%", "src2", $signature_img);
		$rp1 = str_replace("%alt%", "invalid signature", $rp);
		$signature_msg = "The signature is invalid!";
	}
	catch(SignatureFileNotFound $e){
		$rel_id = $usr_c->addReg($usr_id, $data['ID'], 0, 1);
		$rp = str_replace("%path%", "src2", $signature_img);
		$rp1 = str_replace("%alt%", "invalid signature", $rp);
		$signature_msg = "The signature is invalid!";
	}
	catch(SignatureAuthError $e){
		$rel_id = $usr_c->addReg($usr_id, $data['ID'], 0, 3);
	}
	finally{
        $signature_img = $rp1;
		unset($rp);
		unset($rp1);
		$domAdd .= createSignatureCardAuth($data['ID'], $vl);
		$domAdd .= "\n<a href=\"relatory.php?prp_rel=$rel_id\" role=\"button\" class=\"btn btn-block btn-primary\">See relatory</a><br>";
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LPGP Oficial Server</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" href="../css/new-layout.css">
    <script src="../js/main-script.js"></script>
    <link rel="stylesheet" href="../bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap/font-awesome.min.css">
    <script src="../bootstrap/jquery-3.3.1.slim.min.js"></script>
    <script src="../bootstrap/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="../media/logo-lpgp.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.2/popper.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
</head>
<style>
</style>
<body>
    <script>
        $(document).ready(function(){   
            setAccountOpts(true);
            setSignatureOpts();
        });
    </script>
    <div class="container-fluid header-container" role="banner" style="position: relative;">
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
                    <a href="http://localhost/lpgp-server/docs/" class="dropdown-item">Documentation</a>
                    <a href="http://localhost/lpgp-server/about.html" class="dropdown-item">About Us</a>
                    <a href="http://localhost/lpgp-server/contact-us.html" class="dropdown-item">Contact Us</a>
                </div>
            </div>
        </div>

    </div>
    <br>
    <hr>
    <div class="container-fluid container-content" style="position: relative; margin-top: 10%;">
        <div class="row-main row">
            <div class="col-7 clear-content" style="position: relative; margin-left: 21%;">
				<?php
					echo $domAdd;
				?>
                <br>
            </div>
        </div>
    </div>
    <br>
    <div class="footer-container container">
        <div class="footer-row row">
            <div class="footer col-12" style="height: 150px; background-color: black; margin-top: 100%; position: relative; max-width: 100%; left: 0;">
                <div class="social-options-grp">
                    <div class="social-option">
                        <a href="https://github.com/GiullianoRossi1987/lpgp-server" target="_blanck" id="github" class="social-option-footer">
                        <img src="./media/github.png" alt="" width="50px" height="30px"></a>
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