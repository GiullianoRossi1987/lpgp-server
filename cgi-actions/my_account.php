<?php
if(session_status() == PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/core/js-handler.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/core/Core.php";


use function JSHandler\lsSignaturesMA;
use function JSHandler\sendUserLogged;
use const MAX_SIGC;
use function JSHandler\createClientCard;

use Core\ProprietariesData;
use Core\UsersData;
use Core\PropCheckHistory;
use Core\UsersCheckHistory;
use Core\ClientsData;
use Core\ClientsAccessData;

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
        })

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
        <div class="row-main row">
            <div class="col-12 clear-content" style="position: relative; margin-left: 0; max-width: 100% !important">
                <div class="container user-data-con" style="margin-left: 0; max-width: 100%;">
					<div class="main-row row">
                        <div class="main-col col-5 card" style="margin-left: 0 !important; border: none;">
                            <div class="container data-container">
                                <div class="main-row row card-header">
                                    <div class="img-cont card-img-top" style="margin-left: 29%;">
                                        <div id="img-user"></div>
                                    </div>
                                    <br>
                                    <div class="col-12 data-usr">
                                        <br>
                                        <?php
                                        if($_SESSION['mode'] == "prop"){
                                            $dt = $prp->getPropData($_SESSION['user']);
                                            echo "<h1 class=\"user-name\">Name: " . $dt['nm_proprietary'] . "</h1>\n";
                                            echo "<h3 class=\"mode\">Type: Proprietary</h4>\n";
                                            echo "<h3 class=\"email\">Email: " . $dt['vl_email'] . "</h3>\n";
                                            echo "<h3 class=\"date-creation\">Date of creation: " . $dt['dt_creation'] . "</h3>\n";

                                        }
                                        else{
                                            $dt = $usr->getUserData($_SESSION['user']);
                                            echo "<h1 class=\"user-name\"> " . $dt['nm_user'] . "</h1>\n";
                                            echo "<h3 class=\"mode\">Normal User</h4>\n";
                                            echo "<h3 class=\"email\">Email: " . $dt['vl_email'] . "</h3>\n";
                                            echo "<h6 class=\"date-creation\">Date creation: " . $dt['dt_creation'] . "</h3>\n";
                                        }
                                        ?>
                                        <a class="account-separator" id="accountopt-sep" href="#moreoptions-section" data-toggle="collapse" aria-expanded="false" aria-controls="moreoptions-section">
                                            <h2>More account options<span><i class="fas fa-caret-down" style="margin-left: 37%;"></i></span></h2>
                                        </a>
                                        <div class="collapse section" id="moreoptions-section">
                                            <br>
                                            <div class="btn-group" style="margin-left: 7%;">
                                                <a class="img-settings btn btn-lg btn-dark" href="ch_my_data.php" role="button">
                                                    Edit Account
                                                    <span>
                                                        <i class="fas fa-cog"></i>
                                                    </span>
                                                </a>
                                                <button class="btn btn-danger" data-toggle="modal" data-target="#modal-delete" type="button">
                                                    Remove account
                                                    <span>
                                                        <i class="fas fa-times"></i>
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
                                                                <h3>That action can't be undone!</h3>
                                                                <a href="del_account.php?confirm=y" role="button" class="btn btn-lg btn-danger">Yes, delete my account</a>
                                                                <a href="#" role="button" class="btn btn-lg btn-secondary" data-dismiss="modal">Cancel</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>
                                                <?php
                                                if($_SESSION['mode'] == "prop"){
                                                    $id = base64_encode($dt['cd_proprietary']);
                                                    echo "<a href=\"proprietary.php?id=$id\" role=\"button\" target=\"_blanck\" class=\"btn btn-primary btn-lg\">See as another one</a>";
                                                }
                                                else{
                                                    $id = base64_encode($dt['cd_user']);
                                                    echo "<a href=\"user.php?id=$id\" role=\"button\" target=\"_blanck\" class=\"btn  btn-lg btn-primary\">See as another one</a>";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="others-col col-md-7" style="margin-top: 3%;">
                            <div class="signatures-col col-12">
                                <?php if($_SESSION['mode'] == "prop") echo '<a href="#signatures-section" class="account-separator" id="signature-sep" aria-controls="signatures-section" aria-expanded="false" data-toggle="collapse">
                                        <h2 class="mainheader-heading mb-0">My Signatures<span style="margin-left: 70%;">
                                        <i class="fas fa-caret-down"></i>
                                    </span></h2>

                                    </a>';
                                ?>
                                <div id="signatures-section" class="collapse section">
                                    <?php
                                    // Signatures
                                    /////////////////////////////////////////////////////////////////////////////////////////////////
                                    if($_SESSION['mode'] == "prop"){
                                        $prp = new ProprietariesData("giulliano_php", "");
                                        echo lsSignaturesMA($prp->getPropID($_SESSION['user']));
                                        echo "<br>\n<a href=\"create_signature.php\" role=\"button\" class=\"btn btn-block btn-success\">".
                                                    "Create a new signature <span><i class=\"fas fa-id-card\"></i></span>".
                                                    "</a><br>";
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="history-col col-12" style="position: relative; margin-top: 10%;">
                                <a class="account-separator" href="#history-section" data-toggle="collapse" aria-expanded="false" aria-controls="history-section" id="history-sep">
                                    <h2>
                                        My History
                                        <span>
                                            <i class="fas fa-caret-down" style="margin-left: 75%;"></i>
                                        </span>
                                    </h2>
                                </a>
                                <div class="collapse section" id="history-section">
                                    <?php
                                    // History
                                    ///////////////////////////////////////////////////////////////////////////////////////////////
                                    if($_SESSION['mode'] == "prop"){
                                        $obj = new PropCheckHistory("giulliano_php", "");
                                        $hist = $obj->getPropHistory($_SESSION['user']);
                                        $hist_e = explode("<br>", $hist);
                                        for($i = 0; $i <= MAX_SIGC; $i++){
                                            if(isset($hist_e[$i])) echo $hist_e[$i] . "<br>";
                                            else break;
                                        }
                                    }
                                    else{
                                        $obj = new UsersCheckHistory("giulliano_php", "");
                                        $hist = $obj->getUsrHistory($_SESSION['user']);
                                        $hist_e = explode("<br>", $hist);
                                        for($i = 0; $i <= MAX_SIGC; $i++){
                                            if(isset($hist_e[$i])) echo $hist_e[$i] . "<br>";
                                            else break;
                                        }
                                    }
                                    ?>
                                    <a href="my-history.php" role="button" class="btn btn-block btn-primary">
                                        See my history
                                        <span>
                                            <i class="fas fa-history"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-12 clients-col" style="margin-top: 10%;">
                                <?php
                                if($_SESSION['mode'] == "prop") echo '<a href="#clients-section" class="account-separator" data-toggle="collapse" aria-controls="clients-section" aria-expanded="false" id="client-sep">
                                    <h2>
                                        My Clients
                                        <span>
                                            <i class="fas fa-caret-down" style="margin-left: 75%;"></i>
                                        </span>
                                    </h2>
                                </a>'
                                ?>
                                <div class="collapse section" id="clients-section">
                                    <?php
                                    if($_SESSION['mode'] == "prop"){
                                        $obj = new ClientsData("giulliano_php", "");
                                        $clients = $obj->getClientsByOwner($_SESSION['user']);
                                        $hs = new ClientsAccessData("giulliano_php", "");
                                        $dt = "";
                                        if(count($clients) == 0){
                                            echo "<h1>You don't have any clients yet!</h1>";
                                        }
                                        else{
                                            $countLim = 0;
                                            foreach($clients as $client){
                                                $accs = $hs->getAccessClient($client['cd_client']);
                                                $cldt = [$client['cd_client'], $client['nm_client'], count($accs)];
                                                $dt .= createClientCard($cldt) . '<br>';
                                                $countLim++;
                                                if($countLim == 4) break;
                                            }
                                        }
                                        $dt .= '<a href="create-client.php" role="button" class="btn btn-success btn-block">Create a new Client</a>';
                                        echo $dt;
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
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
