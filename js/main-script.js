// there's some uses of the http://localhost/ if you don't want it then just change't

function resetVals(){
    /**
     * Resets the values at the localStorage.
     * The values are:
     *    logged-user => if there's a user logged ("true" or "false")
     *    user_mode   => if the logged user is a proprietary or a normal user ("null", 1, 0). null if there's no user logged. 0 if is a normal, 1 if is a proprietary;
     *    checked     => if the user email was checked ("true", "false", "null")
     *    dark-room   => if the screen will use the dark mode or the light mode (true, false)
     *    switcher_dk => if the switcher of the dark room will be dark or not
     */
    localStorage.setItem("logged-user", "false");
    localStorage.setItem("user_mode", "null");
    localStorage.setItem("checked", "null");
    localStorage.setItem("dark-room", "false");
    localStorage.setItem("switcher-dk", "true");
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

function setOptionByLogin(){
    /**
     * 
     */
    clsLoginOpts();
    var local_opts = document.querySelector("#item1 .options-login");
    if(localStorage.getItem("logged-user") == "true"){
        var opt_account = document.createElement("a");
        var opt_config = document.createElement("a");
        var opt_logoff = document.createElement("a");
        var li_1 = document.createElement("li");
        var li_2 = document.createElement("li");
        var li_3 = document.createElement("li");

        opt_account.classList.add("item-option");
        opt_account.classList.add("login-opt-item");
        opt_account.href = "http://localhost/lpgp-server/cgi-actions/my_account.php";
        opt_account.innerText = "My Account";

        opt_config.classList.add("item-option");
        opt_config.classList.add("login-opt-item");
        opt_config.href = "http://localhost/lpgp-server/cgi-actions/my_account_config.php";
        opt_config.innerText = "My Configurations";

        opt_logoff.classList.add("item-option");
        opt_logoff.classList.add("login-opt-item");
        opt_logoff.href = "http://localhost/lpgp-server/cgi-actions/logoff.php";
        opt_logoff.innerText = "Logoff";

        li_1.appendChild(opt_account);
        li_2.appendChild(opt_config);
        li_3.appendChild(opt_logoff);

        li_1.classList.add("opt-login");
        li_2.classList.add("opt-login");
        li_3.classList.add("opt-login");

        local_opts.appendChild(li_1);
        local_opts.appendChild(li_2);
        local_opts.appendChild(li_3);
    }
    else{
        var opt_login = document.createElement("a");
        var opt_create = document.createElement("a");
        var fldr_login = document.createElement("li");
        var fldr_create = document.createElement("li");

        opt_login.classList.add("item-option");
        opt_login.classList.add("login-opt-item");
        opt_login.href = "http://localhost/lpgp-server/cgi-actions/login.php"; // trade if it's not your used link
        opt_login.innerText = "Login";

        opt_create.classList.add("item-option");
        opt_create.classList.add("login-opt-item");
        opt_create.href = "http://localhost/lpgp-server/create_account.html";
        opt_create.innerText = "Create a Account";

        fldr_create.classList.add("opt-login");
        fldr_create.appendChild(opt_create);

        fldr_login.classList.add("opt-login");
        fldr_login.appendChild(opt_login);

        local_opts.appendChild(fldr_login);
        local_opts.appendChild(fldr_create);
    }
}

function setOptionsbyMode(){
    /**
     * 
     */
    clsSignOpts();
    var local_opts = document.querySelector("#item2 .si-opts");
    if(localStorage.getItem("user_mode") == "1" || localStorage.getItem("user_mode") == 1){
        // is a proprietary account
        var get_opt = document.createElement("a");
        var get_fldr = document.createElement("li");
        var my_sign = document.createElement("a");
        var my_sign_fldr = document.createElement("li");

        get_opt.classList.add("item-option");
        get_opt.classList.add("sig-option-item");
        get_opt.href = "http://localhost/lpgp-server/cgi-actions/get_my_signature.php";
        get_opt.innerText = "Get my signature";

        my_sign.classList.add("item-option");
        my_sign.classList.add("sig-option-item");
        my_sign.href = "http://localhost/lpgp-server/cgi-actions/my_signatures.php";
        my_sign.innerText = "My Signatures";
        
        get_fldr.classList.add("opt-signature");
        get_fldr.appendChild(get_opt);
        my_sign_fldr.classList.add("opt-signature");
        my_sign_fldr.appendChild(my_sign);

        local_opts.appendChild(get_fldr);
        local_opts.appendChild(my_sign_fldr);

        var chk_signature = document.createElement("a");
        var chk_sign_fldr = document.createElement("li");

        chk_signature.classList.add("item-option");
        chk_signature.classList.add("sig-option-item");
        chk_signature.href = "http://localhost/lpgp-server/check_signature.html";
        chk_signature.innerText = "Check a signature";

        chk_sign_fldr.classList.add('opt-signature');
        chk_sign_fldr.appendChild(chk_signature);

        local_opts.appendChild(chk_sign_fldr);
    }
    else if(localStorage.getItem("user_mode") == "0" || localStorage.getItem("user_mode") == 0){
        var chk_signature = document.createElement("a");
        var chk_sign_fldr = document.createElement("li");

        chk_signature.classList.add("item-option");
        chk_signature.classList.add("sig-option-item");
        chk_signature.href = "http://localhost/lpgp-server/check_signature.html";
        chk_signature.innerText = "Check a signature";

        chk_sign_fldr.classList.add('opt-signature');
        chk_sign_fldr.appendChild(chk_signature);

        local_opts.appendChild(chk_sign_fldr);
    }
    else{
        var login_need = document.createElement("a");
        var login_fldr = document.createElement("li");
        
        login_need.classList.add("item-option");
        login_need.classList.add("sig-option-item");
        login_need.href = "http://localhost/lpgp-server/login.html";
        login_need.innerText = "Make login to access the signatures";

        login_fldr.classList.add("opt-signature");
        login_fldr.appendChild(login_need);
        
        local_opts.appendChild(login_fldr);
    }
}

function switchDarkLight(){
    var located_switcher = document.querySelector(".dk-btn");
    var all_bck_oc = document.querySelector(".darkble-dk");
    var all_font_oc = document.querySelector(".darkble-font");
    if(localStorage.getItem("dark-room") == "true"){
        all_bck_oc.style = "background-color: white;";
        all_font_oc.style = "color: black;";
        located_switcher.setAttribute("style", "background-color: black; color: white;");
        located_switcher.innerHTML = "Dark Mode";
    }
    else{
        all_bck_oc.style = "background-color: black;";
        all_font_oc.style = "color: white;";
        located_switcher.setAttribute("style", "background-color: white; color: black;");
        located_switcher.innerHTML = "Light Mode";
        // font.setAttribute("style", "color: black;")  
    }
}

function autoLightDark(){
    if(localStorage.getItem("dark-room") == "true") localStorage.setItem("dark-room", "false");
    else localStorage.setItem("dark-room", "true");
    switchDarkLight();
}
