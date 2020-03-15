<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/js-handler.php";

use function JSHandler\sendUserLogged;
use function JSHandler\setCon1Links;

sendUserLogged();
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
    <script src="./js/actions.js"></script>
    <link rel="stylesheet" href="./bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="./bootstrap/font-awesome.min.css">
    <link rel="stylesheet" href="css/content-style.css">
    <script src="./bootstrap/jquery-3.3.1.slim.min.js"></script>
    <script src="./bootstrap/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="./media/new-logo.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.2/popper.min.js"></script>
    
</head>
<style>
</style>
<body>
    <script>
        $(document).ready(function(){   
            setAccountOpts();
            setSignatureOpts();
        });

        $(document).ready(function(){
            $(".contitle").css("opacity", "1");
            $(".headtitle").css("opacity", "1");
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

        $("#mainCarousel").carousel({
            interval: 2000,
            direction: left
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
    <div class="content1 container content">
        <div class="row rowcontent">
            <div class="col-12 col-md-12 col-sm-12 col-ls-12 main-header" >
                <h1 class="anim-appear masthead-heading text-uppercase mb-0" style="color: black; margin-top: 12%;"><u>LPGP</u></h1>
                <h1 class="masthead-heading text-uppercase mb-0" style="color: black; text-align: center; margin-top: 1%;">Let the golden raven lead you</h1>
            </div>
        </div>
        <div class="row rowcontentn">
            <div class="col-12 col-md-12 col-sm-12 content-nrm" id="con2">
                <div id="logo-ex"></div>
                <h1>What's LPGP?</h1>
                <p>
                    LPGP is a online authenticated certificate. There're so many
                    other 
                </p>
            </div>
        </div>
        <hr>
        <div class="row rowcontentn">

        </div>
    </div>
    <div class="footer-container container">
        <div class="footer-row row">
            <div class="footer col-12">
                <div class="social-options-grp">
                    <div class="social-option">
                        <a href="https://github.com/GiullianoRossi1987/lpgp-server" target="_blanck" id="github" class="social-option-footer">
                        <img src="./media/github.png" alt="" width="50px" height="30px">Visit our github</a>
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
