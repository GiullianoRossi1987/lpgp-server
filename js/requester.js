function loadJquery(internalPath = false){
    let linkB_E = document.createElement("link");
    let scriptB_E = document.createElement("script");
    let scriptJ_E = document.createElement("script");

    if(internalPath){
        linkB_E.href = "../jquery/lib/bootstrap/css/bootstrap.css";
        scriptB_E.src = "../jquery/lib/bootstrap/js/bootstrap.js";
        scriptJ_E.src = "../jquery/lib/jquery-3.4.1.min.js";
    }
    else{
        linkB_E.href = "jquery/lib/bootstrap/css/bootstrap.css";
        scriptB_E.src = "jquery/lib/bootstrap/js/bootstrap.js";
        scriptJ_E.src = "jquery/lib/jquery-3.4.1.min.js";
    }
    document.querySelector("head").appendChild(linkB_E);
    document.querySelector("head").appendChild(scriptB_E);
    document.querySelector("head").appendChild(scriptJ_E);
}

function loadLoggedData(internalPath = false){
    // make shure you loaded the Jquery correctly first
    // for that just call loadJquery(internalPath)
    var mainData = null;
    $.post({
        url: internalPath ? "ajx_logged_request.php" : "cgi-actions/ajx_logged_request.php",
        data: "getJSON=t",
        contentType: false,
        processData: false,
        success: function(response){ mainData = JSON.parse(response); }
        error: function(xhr, status, error){ console.error(error); }
    });
    return mainData;
}
