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
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="./media/logo-lpgp.png" type="image/x-icon">
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
                $("#err-lb-passwd1").css("visibility", "visible");
            }
            else if(content != $("#password2").val()){
                $("#err-lb-passwd1").text("The passwords doesn't match");
                $("#err-lb-passwd1").css("visibility", "visible");
            }
            else{
                $("#err-lb-passwd1").css("visibility", "hidden");
                $("#err-lb-passwd2").css("visibility", "hidden");
            }
        });

        $(document).on("change", "#password2", function(){
            var content = $(this).val();
            if(content.length <= 7){
                $("#err-lb-passwd2").text("Please choose a password with more then 7 characters.");
                $("#err-lb-passwd2").css("visibility", "visible");
            }
            else if(content != $("#password1").val()){
                $("#err-lb-passwd2").text("The passwords doesn't match");
                $("#err-lb-passwd2").css("visibility", "visible");
            }
            else {
                $("#err-lb-passwd2").css("visibility", "hidden");
                $("#err-lb-passwd1").css("visibility", "hidden");
            }
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

        $(document).on("click", "#upload-img-btn", function(){
            $("#upload-img-input").show();
            $("#default-img").attr("checked", false);
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
    <div class="container-fluid container-content" style="position: absolute;">
        <div class="row-main row">
            <div class="col-6 clear-content" style="position: absolute; margin-left: 23%;">
                <br>
                <div class="page-title-container container">
                    <div class="row">
                        <div class="col-12 page-title-ns">
                            <center>
                                <h1>Create account</h1>
                            </center>
                        </div>
                    </div>
                </div>
                <br>
                <form action="https://localhost/lpgp-server/cgi-actions/create_account.php" method="post" enctype="multipart/form-data">
                    <label for="username" class="form-label">
                        <h4>Pick a Username</h4>
                    </label>
                    <br>
                    <input type="text" name="username" class="form-control" id="username" placeholder="Username">
                    <br>
                    <label for="username" class="form-label" aria-hidden="true">
                        <small class="error-lb" id="err-lb-username"></small>
                    </label>
                    <br>
                    <label for="email" class="form-label">
                        <h4>Pick a e-mail address</h4>
                    </label>
                    <br>
                    <input type="email" id="email" name="email" placeholder="user@example.com" class="form-control">
                    <br>
                    <label for="email" class="form-label">
                        <small class="error-lb" id="err-lb-email"></small>
                    </label>
                    <br>
                    <label for="password1" class="form-label">
                        <h4>Pick a Password</h4>    
                    </label>
                    <br>
                    <label for="password1" class="form-label">
                        <small class="error-lb" id="err-lb-passwd1"></small>
                    </label>
                    <br>
                    <input type="password" class="form-control" name="password1" id="password1" placeholder="Password">
                    <br>
                    <label for="password1" class="form-label">
                        <button class="btn btn-secondary show-passwd-btn" id="show-passwd1" type="button">Show my password</button>
                    </label>
                    <br>
                    <label for="password-c" class="form-label">
                        <h4>Confirm the password</h4>
                    </label>
                    <br>
                    <label for="password-c" class="form-label">
                        <small class="error-lb" id="err-lb-passwd2"></small>
                    </label>
                    <br>
                    <input type="password" name="password2" class="form-control" id="password2" placeholder="Confirm the password">
                    <br>
                    <label for="password2" class="form-label">
                        <button class="btn btn-secondary show-passwd-btn" id="show-passwd2" type="button">Show my password</button>
                    </label>
                    <br>
                    <hr>
                    <h4>Choose a account type: </h4>
                    <br>
                    <div class="form-check form-check-inline flex-opt-acc">
                        <div class="types-opts">
                            <div class="type-opt">
                                <input type="radio" name="account-mode" class="form-check-input type-inp" value="proprietary" id="prop-rd">
                                <label for="prop-rd" class="form-label prop opt-lb"></label>
                            </div>
                            <div class="type-opt">
                                <input type="radio" name="account-mode" class="form-check-input type-inp" value="normal" id="normie-rd">
                                <label for="normie-rd" class="form-label normie-flex-rb opt-lb"></label>
                            </div>
                        </div>
                        <br>
                        <a data-toggle="collapse info info-sm" href="#typesTB" aria-expanded="false" aria-controls="typesTB">
                            a
                        </a>
                        <br>
                        <div class="collapse" id="typesTB" aria-hidden="true">
                            <h3>What's account types?</h3>
                            <hr>
                            <p>
                                The account types are the ways to control the actions of each type of user,
                                There're two account types:
                                <ol>
                                    <li>
                                    <h6>Normal account:</h6>
                                    <p>
                                        That account type's the more 
                                    </p>
                                    </li>
                                </ol>
                            </p>
                            <table>
                            </table>
                        </div>
                    </div>
                    <br>
                    <h4>Choose your profile image</h4>
                    <br>
                    <div class="img-select-container">
                        <div class="img-radio-default">
                            <input type="radio" class="form-check" id="default-img" name="img-user" value="default-img" checked>
                            <label for="default-img" id="default-img-lb">
                            </label>
                        </div>
                        <br>
                        <button type="button" class="btn btn-primary btn-lg" data-toggle="collapse" id="upload-img-btn" data-target="#upload-img-input" aria-expanded="false" aria-controls="upload-img-input">
                            Upload a local image
                        </button>
                        <br>
                        <div class="collapse" id="upload-img-input" aria-hidden="true"> 
                            <label for="img-uploaded" class="form-label">
                                <h5>Upload a Image</h5>
                            </label>
                            <input type="file" name="img-user[]" id="img-uploaded" class="form-control">
                        </div>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-block btn-success">Create account</button>
                </form>
                <br>
            </div>
        </div>
    </div>
    <br>
    <div class="footer-container container">
        <div class="footer-row row">
            <div class="footer col-12" style="height: 150px; background-color: black; margin-top: 90%; position: absolute; max-width: 100%; left: 0;">
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