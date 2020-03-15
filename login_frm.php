<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/js-handler.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";

use function JSHandler\sendUserLogged;
use templateSystem\ErrorTemplate;

sendUserLogged();
if(isset($_SESSION['logged-user']) && $_SESSION['logged-user']){
    $err = $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/templates/500-error-internal.html";
    $tpl = new ErrorTemplate($err, "UNKNOWN ERROR: that page souldn't appear to a logged user!", "login_frm.php", null, "<a role=\"button\" class=\"default-btn-err btn\" href=\"https://localhost/lpgp-server\"></a>");
    die($tpl->parseFile());
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
    <link rel="stylesheet" href="./css/new-layout.css">
    <script src="./js/main-script.js"></script>
    <link rel="stylesheet" href="./bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="./bootstrap/font-awesome.min.css">
    <script src="./bootstrap/jquery-3.3.1.slim.min.js"></script>
    <script src="./bootstrap/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="./media/logo-lpgp.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.2/popper.min.js"></script>
    <link href="./css/content-style.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/login-fmr.css">
</head>
<body>
    <script>
        $(document).ready(function(){   
            setAccountOpts();
            setSignatureOpts();
        });

        var passwd_vl = "text";
        $(document).on("click", "#show-passwd", function(){
            $("#password").attr("type", passwd_vl);
            if(passwd_vl == "text") passwd_vl = "password";
            else passwd_vl = "text";
        });

        $(document).on("change", "#password", function(){
            var vl = $("#password").val();
            if(vl.length <= 0){
                $("#err-lb").text("Please choose a password!");
                $("#err-lb").css("visibility", "visible");
            }
            else{
                $("#err-lb").css("visibility", "none");
            }
        });
    </script>
    <br>
    <div class="container-fluid container-content" style="position: relative;">
        <div class="row-main row">
            <div class="col-4 clear-content center-content" style="position: relative; margin-left: 32% !important; margin-top: 5% !important; border-radius: 6%;">
                <div id="logo-ex" style="margin-left: 42% !important;"></div>
                <form action="./cgi-actions/login.php" method="POST">
                    <h1 style="margin-left: 42%;" class="masthead-heading mb-0">Login</h1>
                    <br>
                    <label for="username">
                        <h5>Your name</h5>
                    </label>
                    <br>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username">
                    <br>
                    <label for="password">
                        <h5>Your Password</h5>
                    </label>
                    <br>
                    <input type="password" class="form-control default-input" id="password" name="password" placeholder="Password">
                    <br>
                    <label for="password">
                        <button class="btn btn-secondary" id="show-passwd" type="button">
                            Show Password
                        </button>
                    </label>
                    <label for="password">
                        <small id="err-lb" style="visibility: none" class="error-lb">
                        </small>
                    </label>
                    <br>
                    <hr>
                    <h3>Account type</h3>
                    <br>
                    <div class="form-check form-check-inline">
                        <div id="nrml-rd">
                            <input type="radio" value="normal" name="account-type" class="form-check-input" id="normal-rd">
                            <label for="normal-rd" class="form-check-label">Normal user</label>
                        </div>
                        <div class="propp-rd">
                            <input type="radio" value="proprietary" name="account-type" class="form-check-input" id="prop-rd">
                            <label for="prop-rd" class="form-check-label">Proprietary</label>
                        </div>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-success btn-block">Login</button>
                    <small>
                        <a href="create_account_frm.php">
                            Don't have a account? Create one!
                        </a>
                    </small>
                    <br>
                </form>
            </div>
        </div>
    </div>
</body>
</html>