<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LPGP Oficial Server</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" href="../css/layout.css">
    <script src="../js/main-script.js"></script>
    <link rel="stylesheet" href="../bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap/font-awesome.min.css">
    <script src="../bootstrap/jquery-3.3.1.slim.min.js"></script>
    <script src="../bootstrap/bootstrap.min.js"></script>
</head>
<body>
    <script>
        $(document).on("click", "#login-opt", function(){
            $("#item1").toggle("slide");
        });

        $(document).on("click", "#sign-opts-btn", function(){
            $("#item2").toggle("slide");
        });

        $(document).ready(function(){
            setOptionByLogin();
            setOptionsbyMode();
            switchDarkLight("dk-btn");
        });

        $(document).on("click", "#help-handler", function(){
            $("#item3").toggle("slide");
        })
    </script>

    <div class="container-fluid side-bar-main">
        <div class="row main-row">
            <div class="col-md-3 side-bar">
                <div class="container main-cont">
                    <div class="row title-row">
                        <h1 class="dft-title">LPGP Oficial Server</h1>
                        <h6 class="dft-subtitle">Your work certain!</h6>
                    </div>
                </div>
                <div class="container link-list-container">
                    <div class="row links-list-row">
                        <ul class="link-list list-unstyled">
                            <li class="link-item">
                                <button data-toggle="collapse" class="item-handler btn" aria-expanded="false" aria-controls="collapse" data-target="item1" type="button" id="login-opt">
                                    Login Options
                                </button>
                                <div class="collapse item-opts" id="item1">
                                    <ul class="options-login list-unstyled">
                                        <li class="opt-login">
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="link-item">
                                <button data-toggle="collapse" class="item-handler btn" aria-expanded="false" aria-controls="collapse" data-target="item2" type="button" id="sign-opts-btn">
                                    Signature options
                                </button>
                                <div class="collapse item-opts" id="item2">
                                    <ul class="si-opts list-unstyled">

                                    </ul>
                                </div>
                            </li>
                            <li class="link-item">
                                <button class="item-handler btn" id="help-handler">
                                    Help
                                </button>
                                <div class="collapse item-opts" id="item3">
                                    <ul class="help-opts list-unstyled">
                                        <li class="help-opt">
                                            <a href="./contact.html">Contact us</a>
                                        </li>
                                        <li class="help-opt">
                                            <a href="./docs/index.html">Documentation</a>
                                        </li>
                                        <li class="help-opt">
                                            
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="container dk-btn-container">
                        <div class="dk-row row">
                            <button class="dk-btn btn" onclick="autoLightDark();"></button>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    </div>
    <div class="container content-container">
        <div class="row main-row">
            <div class="content col-12 darkble-dk">
                <h1 class="darkble-font">LPGP-server</h1>
                <h1 class="darkble-font">Check your email code 
                    <?php echo $_SESSION['user']; ?>
                </h1>
                <form action="http://localhost/lpgp-server/cgi-actions/check_email.php" method="POST">
                    <label for="code" class="darkble-font">Code: </label>
                    <input type="text" required name="code" id="code">
                    <br>
                    <button class="darkble-btn btn default-btn" id="btn-send" type="submit" value="Send Code" name="btn-code">Send Email</button>
                    <button class="darkble-btn btn default-btn" id="btn-resend" type="button" value="Resend email" name="btn-resend">Resend Email</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>