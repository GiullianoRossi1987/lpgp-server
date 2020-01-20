<?php 
if(session_status() == PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/js-handler.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";

use function JSHandler\lsSignaturesMA;
use function JSHandler\sendUserLogged;

use Core\ProprietariesData;
use Core\UsersData;
use Core\PropCheckHistory;
use Core\UsersCheckHistory;

sendUserLogged(); // preventing bugs

$prp = new ProprietariesData("giulliano_php", "");
$usr = new UsersData("giulliano_php", "");
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
    <link rel="stylesheet" href="./bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="./bootstrap/font-awesome.min.css">
    <script src="../bootstrap/jquery-3.3.1.slim.min.js"></script>
    <script src="../bootstrap/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="../media/logo-lpgp.png" type="image/x-icon">
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
            $("#img-user").css("background-image", "url(" + "." + getLinkedUserIcon() + ")");
        });

        $(document).scroll(function(){
            $(".header-container").toggleClass("scrolled", $(this).scrollTop() > $(".header-container").height());
            $(".default-btn-header").toggleClass("default-btn-header-scrolled", $(this).scrollTop() > $(".header-container").height());
            $(".opts").toggleClass("opts-scrolled", $(this).scrollTop() > $(".header-container").height());
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
                        <a href="http://localhost/lpgp-server/docs/" class="dropdown-item">Documentation</a>
                        <a href="http://localhost/lpgp-server/about.html" class="dropdown-item">About Us</a>
                        <a href="http://localhost/lpgp-server/contact-us.html" class="dropdown-item">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <hr>
    <div class="container-fluid container-content" style="position: absolute;">
        <div class="row-main row">
            <div class="col-12 clear-content" style="position: relative; margin-left: 0;">
                <div class="container user-data-con">
					<div class="main-row row">
                        <div class="main-col col-12" style="margin-left: 0 !important;">
                            <div class="container data-container">
                                <div class="main-row row">
                                    <div class="img-cont">
                                        <div id="img-user"></div>
                                    </div>
                                    <div class="col-6 data">
                                        <?php
                                        if($_SESSION['mode'] == "prop"){
                                            $dt = $prp->getPropData($_SESSION['user']);
                                            echo "<h1 class=\"user-name\"> " . $dt['nm_proprietary'] . "</h1>\n";
                                            echo "<h4 class=\"mode\">Proprietary</h4>\n";
                                            echo "<h4 class=\"email\">Email: " . $dt['vl_email'] . "</h3>\n";
                                            echo "<h5 class=\"date-creation\">Date of creation: " . $dt['dt_creation'] . "</h3>\n";

                                        }
                                        else{
                                            $dt = $usr->getUserData($_SESSION['user']);
                                            echo "<h1 class=\"user-name\"> " . $dt['nm_user'] . "</h1>\n";
                                            echo "<h4 class=\"mode\">Normal User</h4>\n";
                                            echo "<h4 class=\"email\">Email: " . $dt['vl_email'] . "</h3>\n";
                                            echo "<h5 class=\"date-creation\">Date creation: " . $dt['dt_creation'] . "</h3>\n";
                                        }
                                        ?>
                                        <a class="img-settings btn btn-secondary" href="https://localhost/lpgp-server/cgi-actions/ch_my_data.php" id="img-settings" role="button">
                                            Edit Account
                                            <span>
                                                <img src="../media/settings.png" alt="" width="50px" height="50px">
                                            </span>
                                        </a>
                                        <button class="btn btn-danger" id="del-btn" data-toggle="modal" data-target="#modal-delete" type="button">
                                            Remove account
                                            <span>
                                                <img src="../media/delete-sign.png" alt="" width="50px" height="50px">
                                            </span>
                                        </button>
                                        <div class="modal" id="modal-delete" tabindex="-1" aria-labelledby="del-btn" aria-hidden="true" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title">Are you sure about delete your account?</h3>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <hr>
                                                    <div class="modal-body">
                                                        <a href="https://localhost/lpgp-server/cgi-actions/del_account.php?confirm=y" role="button" class="btn btn-lg btn-danger">Yes, delete my account</a>
                                                        <a href="#" role="button" class="btn btn-lg btn-secondary" data-dismiss="modal">Cancel</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <hr>
                                        <?php
                                        if($_SESSION['mode'] == "prop"){
                                            $id = $dt['cd_proprietary'];
                                            echo "<a href=\"https://localhost/lpgp-server/cgi-actions/proprietary.php?id=$id\" role=\"button\" target=\"_blanck\" class=\"btn btn-lg bt-primary\">See as another one</a>";
                                        }
                                        else{
                                            $id = $dt['cd_user'];
                                            echo "<a href=\"https://localhost/lpgp-server/cgi-actions/user.php?id=$id\" role=\"button\" target=\"_blanck\" class=\"btn  btn-primary\">See as another one</a>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="others-col col-12">
                        <?php
                            // Signatures
                            /////////////////////////////////////////////////////////////////////////////////////////////////
                            if($_SESSION['mode'] == "prop"){
                                echo "<h1 class=\"section-title\">My signatures</h1><br>";
                                $prp = new ProprietariesData("giulliano_php", "");
                                echo lsSignaturesMA($prp->getPropID($_SESSION['user']));
                                echo "<br>\n<a href=\"https://localhost/lpgp-server/cgi-actions/create_signature.php\" role=\"button\" class=\"btn btn-block btn-success\">Create a new signature</a>";
                            }
                        ?>
                        </div>
                        <hr>
                        <div class="history-col col-12">
                            <h1 class="section-title">My History</h1>
                            <?php
                            // History
                            ///////////////////////////////////////////////////////////////////////////////////////////////
                            if($_SESSION['mode'] == "prop"){
                                $obj = new PropCheckHistory("giulliano_php", "");
                                $hist = $obj->getPropHistory($_SESSION['user']);
                                $hist_e = explode("<br>", $hist);
                                for($i = 0; $i <= 6; $i++){
                                    if(isset($hist_e[$i])) echo $hist_e[$i] . "<br>";
                                    else break;
                                }
                            }
                            else{
                                $obj = new UsersCheckHistory("giulliano_php", "");
                                $hist = $obj->getUsrHistory($_SESSION['user']);
                                $hist_e = explode("<br>", $hist);
                                for($i = 0; $i <= 6; $i++){
                                    if(isset($hist_e[$i])) echo $hist_e[$i] . "<br>";
                                    else break;
                                }
                            }
                            echo "<a href=\"https://localhost/lpgp-server/cgi-actions/my-history.php\" role=\"button\" class=\"btn btn-block btn-primary\">See all my history</a><br>";
                            ?>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
    <br>
    <div class="footer-container container">
        <div class="footer-row row">
            <div class="footer col-12" style="height: 150px; background-color: black; margin-top: 100%; position: absolute; max-width: 100%; left: 0;">
                <div class="social-options-grp">
                    <div class="social-option">
                        <a href="https://github.com/GiullianoRossi1987/lpgp-server" target="_blanck" id="github" class="social-option-footer">
                        <img src="../media/github.png" alt="" width="50px" height="30px"></a>
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