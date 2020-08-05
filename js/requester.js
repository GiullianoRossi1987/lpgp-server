
function loadJquery(internalPath){
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


function previewImageTmp(internalPath, imgInput){
    var source = null;
    let imgData = new FormData();
    imgData.append("img-auto-load", $(imgInput)[0].files[0]);
    $.post({
        url: internalPath ? "../cgi-actions/ajx_img_viewer.php" : "cgi-actions/ajx_img_viewer.php",
        data: imgData,
        processData: false,
        contentType: false,
        success: function(response){ source = response; }
    });
    return source;
}


function requestChart(client, mode, chartDisposeId){
    var data = client !== null && client != 0 ? "client="+parseInt(client)+"&mode="+parseInt(mode) : "mode="+parseInt(mode);
    $.post({
        url: "ajx_chart_view.php",
        data: data,
        success: function(response){ eval(response.replace("<script>", "").replace("</script>", "")); },
        error: function(xhr, status, error){ console.error(error); }
    });
}


/**
 * That method sends the search content of the main-query.php to the ajax
 * interpreter ajx_query_main.php .
 * @param string scope The scope of the search, if it's in all the whole server ('all')
 *                     or just in the account ('me'); [the account search is available
 *                     only to the proprietaries]
 * @param string needle The needle name to search
 * @param int type The type of the result => 0 : For all; 1: Only Accounts; 2: Only
 *                 Proprietaries Accounts; 3: Only Normal Accounts; 4: Only clients
 * @param disposeResults The ID of the location to dispose the results.
 */
function requestQuery(needle, scope, mode, disposeResults){
    $.post({
        url: "ajx_query_main.php",
        data: "scope="+scope+"&needle="+needle+"&mode="+parseInt(mode),
        dataType: 'text',
        success: function(response){ $(disposeResults).html(response); },
        error: function(xhr, status, error){ console.error(error); }
    });
}
