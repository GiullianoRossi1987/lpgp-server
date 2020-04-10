// there's some uses of the http://localhost/ if you don't want it then just change't

/**
 * Resets the values at the localStorage.
 * The values are:
 *    logged-user => if there's a user logged ("true" or "false")
 *    user_mode   => if the logged user is a proprietary or a normal user ("null", 1, 0). null if there's no user logged. 0 if is a normal, 1 if is a proprietary;
 *    checked     => if the user email was checked ("true", "false", "null")
 *    dark-room   => if the screen will use the dark mode or the light mode (true, false)
 *    switcher_dk => if the switcher of the dark room will be dark or not
 */
function resetVals(){
    localStorage.setItem("logged-user", "false");
    localStorage.setItem("user_mode", "null");
    localStorage.setItem("checked", "null");
    localStorage.setItem("user-icon", "null");
}

function clsLoginOpts(){
    try{
        document.querySelectorAll("#item1 .options-login .opt-login").remove();
    }
    catch(ex){ } // do nothing
}

function clsSignOpts(){
    try{
        for(var i in document.querySelectorAll("#item2 .si-opts .opt-signature")){
            document.querySelector("#item2 .si-opts").removeChild(i);
        }
    }
    catch(ex){}  // do nothing;
}

function setAccountOpts(ext_fls = false){
    /**
     * 
     */
    clsLoginOpts();
    rmClientDrop();
    var local_opts = document.querySelector(".login-dropdown .dropdown-menu");
    if(localStorage.getItem("logged-user") == "true"){
        var account_opt = document.createElement("a");
        var logoff_opt = document.createElement("a");
        var config_opt = document.createElement("a");

        config_opt.href = "https://" + window.location.hostname + "/lpgp-server/cgi-actions/ch_my_data.php";
        logoff_opt.href = "https://" + window.location.hostname + "/lpgp-server/cgi-actions/logoff.php";
        account_opt.href = "https://"+ window.location.hostname + "/lpgp-server/cgi-actions/my_account.php";

        // classes
        config_opt.classList.add("dropdown-item");
        logoff_opt.classList.add("dropdown-item");
        account_opt.classList.add("dropdown-item");

        config_opt.innerText = "Configurations";
        logoff_opt.innerText = "Logoff";
        account_opt.innerText = "My account";
        
        local_opts.appendChild(config_opt);
        local_opts.appendChild(logoff_opt);
        local_opts.appendChild(account_opt);
        setClientsDrop();
        var img = document.createElement("img");
        img.width = 30;
        img.height = 30;
        var local_opt_btn = document.querySelector("#account-opts");
        img.src = getLinkedUserIcon();
        img.classList.add("user-icon");
        document.querySelector("#account-opts span").remove();
        local_opt_btn.appendChild(img);
    }
    else{
        var login_opt = document.createElement("a");
        var ct_accopt = document.createElement("a");

        login_opt.href = "https://"+  window.location.hostname + "/lpgp-server/login_frm.php";
        ct_accopt.href = "https://"+  window.location.hostname + "/lpgp-server/create_account_frm.php";
        login_opt.classList.add("dropdown-item");
        ct_accopt.classList.add("dropdown-item");
        login_opt.innerText = "Make login";
        ct_accopt.innerText = "Create Account";

        local_opts.appendChild(login_opt);
        local_opts.appendChild(ct_accopt);

        var err = false;
        try{
            document.querySelector(".user-icon").remove();
            document.querySelector(".nm-tmp").remove();
        }
        catch(error){
            console.log("There's no image to remove!");
            err = true;
        }
        if(!err){
            var sp = document.createElement("span");
            sp.innerHTML = "Account";
            document.querySelector("#account-opts").appendChild(sp);
        }
    }
}

function rmClientDrop(){
    try{
        document.querySelector("#my-clients").remove();
        document.querySelector("#add-client").remove();
        document.querySelector("#check-client").remove();
    }
    catch($e) {}
}

/**
 * 
 */
function setSignatureOpts(){
    clsSignOpts();
    var local_opts = document.querySelector(".signatures-dropdown .dropdown-menu");
    if(localStorage.getItem("user_mode") == "prop"){
        // is a proprietary account
        var che_sig = document.createElement("a");
        var my_sign = document.createElement("a");
        var devcenter = document.createElement("a");

        my_sign.innerText = "My Signatures";
        che_sig.innerText = "Check a Signature";
        devcenter.innerText = "DevCenter";
        my_sign.href = "https://" + window.location.hostname +"/lpgp-server/cgi-actions/my_signatures.php";
        che_sig.href = "https://" + window.location.hostname +"/lpgp-server/check_signature.php";
        devcenter.href = "https://" + window.location.hostname + "/lpgp-server/devcenter/";
        my_sign.classList.add("dropdown-item");
        che_sig.classList.add("dropdown-item");
        devcenter.classList.add("dropdown-item");


        local_opts.appendChild(my_sign);
        local_opts.appendChild(che_sig);
        local_opts.appendChild(devcenter);
    }
    else if(localStorage.getItem("user_mode") == "normie"){
        var chk_signature = document.createElement("a");

        chk_signature.innerText = "Check a Signature";
        chk_signature.href = "https://" + window.location.hostname + "/lpgp-server/check_signature.php";
        chk_signature.classList.add("dropdown-item");

        local_opts.appendChild(chk_signature);
    }
    else{
        var login_need = document.createElement("a");
        login_need.innerText = "Make login for check a signature";
        login_need.href = "https://" + window.location.hostname +"/lpgp-server/login_frm.php";
        login_need.classList.add("dropdown-item");
        local_opts.appendChild(login_need);
        delete(login_need);
    }
}

function setClientsDrop(){
    if(localStorage.getItem("user_mode") == "prop"){
        var localTo = document.querySelector(".login-dropdown .dropdown-menu");
        var optAdd = document.createElement("a");
        var optMy = document.createElement("a");
        var optCh = document.createElement("a");

        optAdd.classList.add("dropdown-item");
        optMy.classList.add("dropdown-item");
        optCh.classList.add("dropdown-item");

        optAdd.href = "https://" + window.location.hostname + "/lpgp-server/cgi-actions/create-client.php";
        optAdd.innerText = "Create a Client";
        optAdd.id = "add-client";

        optMy.href = "https://" + window.location.hostname + "/lpgp-server/cgi-actions/my-clients.php";
        optMy.innerText = "My clients";
        optMy.id = "my-clients";

        optCh.href = "https://" + window.location.hostname + "/lpgp-server/cgi-actions/check-client.php";
        optCh.innerText = "Check client authentication";
        optCh.id = "check-client";

        localTo.appendChild(optAdd);
        localTo.appendChild(optMy);
        localTo.appendChild(optCh);
    }
}

/**
 * 
 * @param {*} message 
 */
function showError(message){
    /**
     * 
     */
    var error_lbs = document.querySelector(".error-lb");
    error_lbs.innerHTML = message;
    error_lbs.setAttribute("style", "visibility: visible;");
}

function hideError(){
    var error_lbs = document.querySelector(".error-lb");
    error_lbs.setAttribute("style", "visibility: hidden;");
}

function getLinkedUserIcon(){
    var ls  = localStorage.getItem("user-icon").split("/");
    return "https://" + window.location.hostname + "/lpgp-server/" + ls[ls.length - 2] + "/" + ls[ls.length - 1];
}