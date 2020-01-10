<?php session_start(); ?>
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
    <script src="./bootstrap/jquery-3.3.1.slim.min.js"></script>
    <script src="./bootstrap/bootstrap.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="../media/logo-lpgp.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.2/popper.min.js"></script>

</head>
<body>
    <script>
        $(document).ready(function(){   
            setAccountOpts();
            setSignatureOpts();
        });

        $(document).on("change", "#code", function(){
            var content = $(this).val();
            if(content.length <= 0){
                $("#err-lb-code").text("Please insert a valid code!");
                $("#err-lb-code").show();
            }
            else $("#err-lb-code").hide();
        });
    </script>
    <div class="container-fluid header-container" role="banner" style="background-color: rgb(51, 51, 71); position: absolute;">
        <div class="col-12 header" style="height: 71px">
            <div class="opt-dropdown dropdown login-dropdown">
                <button type="button" class="btn btn-lg default-btn-header dropdown-toggle" data-toggle="dropdown" id="account-opts" aria-haspopup="true" aria-expanded="false">
                    Account
                </button>
                <div class="dropdown-menu opts" aria-labelledby="account-opts"></div>
            </div>
            <div class="opt-dropdown dropdown after-opt signatures-dropdown">
                <button class="dropdown-toggle btn btn-lg default-btn-header" data-toggle="dropdown" aria-expanded="false" aria-haspopup="true" id="signature-opts">
                    Signatures
                </button>
                <div class="dropdown-menu opts" aria-labelledby="signature-opts"></div>
            </div>
            <div class="opt-dropdown dropdown after-opt help-dropdown" style="left: 12%; position: absolute !important;">
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

    <div class="container-fluid container-content" style="position: absolute;margin-left: 23%;">
        <div class="row-main row">
            <div class="col-7 clear-content">
                <h1>Check your email <?php echo $_SESSION['user'];?></h1>
                <br>s
                <form action="check_email.php" method="post">
                    <label for="code" class="form-label">
                        <h4>Insert your e-mail code</h4>
                    </label>
                    <br>
                    <input type="text" id="code" name="code" placeholder="Your code" class="form-control">
                    <br>
                    <label for="code" class="form-label">
                        <small class="error-label" id="err-lb-code"></small>
                    </label>
                    <br>
                    <div class="button-group default-group">
                        <button class="btn btn-lg btn-secondary" name="btn-resend">Resend the email</button>
                        <button class="btn btn-lg btn-success" name="bt-code">Submit code</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>