<?php
if(session_status() == PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/js-handler.php";

use Core\UsersData;
use Core\ProprietariesData;
use function JSHandler\sendUserLogged;

sendUserLogged();
$error_msg = "";
$err = false;

if($_SESSION['mode'] == "prop"){
	$prp = new ProprietariesData("giulliano_php", "");
	$data = $prp->getPropData($_SESSION['user']);
}
else{
	$usr = new UsersData("giulliano_php", "");
	$data = $usr->getUserData($_SESSION['user']);
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
    <link rel="shortcut icon" href="../media/new-logo.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.2/popper.min.js"></script>
</head>
<style>
</style>
<body>
    <script>
        $(document).ready(function(){   
            setAccountOpts(true);
            setSignatureOpts();
            $("#avatar-ep").css("background-image", "url(" + getLinkedUserIcon() + ")");
        });

        var pas1 = "text";
        var pas2 = "text";
        var vb = "visible";

        $(document).on("click", "#show-passwd1", function(){
            $("#password1").attr("type", pas1);
            if(pas1 == "text") pas1 = "password";
            else pas1 = "text";
        });

        $(document).on("click", "#show-passwd2", function(){
            $("#password2").attr("type", pas1);
            if(pas2 == "text") pas2 = "password";
            else pas2 = "text";
        });

        $(document).on("change", "#password1", function(){
            var content = $(this).val();
            if(content.length <= 7){
                $("#err-lb-passwd1").text("Please choose a password with more then 7 characters.");
                $("#err-lb-passwd1").show();
            }
            else if(content != $("#password2").val()){
                $("#err-lb-passwd1").text("The passwords doesn't match");
                $("#err-lb-passwd1").show();
            }
            else $("#err-lb-passwd1").hide();
        });

        $(document).on("change", "#username", function(){
            var content = $(this).val();
            if(content.length <= 0){
                $("#err-lb-username").text("Please choose a username!");
                $("#err-lb-username").show();
            }
            else $("#err-lb-username").hide();
        });

        $(document).on("change", "#email", function(){
            var content = $(this).val();
            if(content.length <= 0){
                $("#err-lb-email").text("Please choose a e-amil address");
                $("#err-lb-email").show();
            }
            else if(content.search("@") < 0){
                $("#err-lb-email").text("Please choose a valid e-mail address");
                $("#err-lb-email").show();
            }
            else $("#err-lb-email").hide();
        });

        $(document).on("click", "#default-img", function(){
            $("#upload-img-input").hide();
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
                            <a href="http://localhostdocs/" class="dropdown-item">Documentation</a>
                            <a href="http://localhostabout.html" class="dropdown-item">About Us</a>
                            <a href="http://localhostcontact-us.html" class="dropdown-item">Contact Us</a>
                        </div>
                    </div>
                </div>
			</div>
		</div>
	</div>
    <br>
    <hr>
    <div class="container-fluid container-content" style="position: relative;">
        <div class="row-main row">
            <div class="col-7 clear-content" style="position: relative; margin-left: 21%; margin-top: 10%;">
				<form action="change_account_data.php" method="post" class="form-group" enctype="multipart/form-data">
                    <h1>Your configurations</h1>
                    <h6>If you don't want to change a field just leave it empty</h6>
                    <br>
                    <div id="avatar-ep" style="width: 200px; height: 200px; background-size: cover; background-repeat: no-repeat">
                    </div>
                    <label for="new-img" class="form-label">Change the profile image</label>
                    <br>
                    <input type="file" name="new-img[]" id="new-img" class="form-group" accept="image/*">
					<br>
                    <label for="username" class="form-label">Change your username</label>
                    <br>
					<input type="text" name="username" id="username" class="form-control">
					<br>
                    <label for="email" class="form-label">Change your email</label>
                    <br>
					<input type="email" name="email" id="email" class="form-control">
					<br>
                    <label for="passwd1" class="form-label">Change your password</label>
                    <br>
					<input type="password" id="passwd1" name="passwd" class="form-control">
					<label for="passwd1" class="form-label">
                        <br>
						<button type="button" class="btn btn-md btn-secondary" id="spass1">
							Show password
						</button>
                    </label>
					<br>
                    <label for="passwd2" class="form-label">Confirm the new password</label>
                    <br>
					<input type="password" name="passwd-confirm" id="passwd2" class="form-control">
					<label for="passwd2" class="form-label">
                        <button type="button" class="btn btn-md btn-secondary" id="spass2">Show password</button>
                        <br>
					</label>
					<br>
					<button class="btn btn-lg btn-success" type="submit">Save configurations</button>
					<button class="btn btn-lg btn-secondary" type="submit" onclick="window.location.replace('./my_account.php');">Cancel</button>
				</form>
            </div>
		</div>
	</div>
    <br>
    <div class="footer-container container">
        <div class="footer-row row">
            <div class="footer col-12" style="height: 150px; background-color: black; margin-top: 190%; position: relative !important; max-width: 100%; left: 0;">
                <div class="social-options-grp">
                    <div class="social-option">
                        <a href="https://github.com/GiullianoRossi1987/lpgp-server" target="_blanck" id="github" class="social-option-footer">
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