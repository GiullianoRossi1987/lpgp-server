<?php
namespace JSHandler;
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";

use Core\SignaturesData;

/**
 * That method sends the $_SESSION vars about the logged user to the localStorage. In a inexisting session
 * It will set such as no one logged at the system.
 *
 * @return void
 */
function sendUserLogged(){
    if(session_status() == PHP_SESSION_NONE || session_status() == PHP_SESSION_DISABLED){
        // if there's no one logged.
        echo "<script>\nlocalStorage.setItem(\"logged-user\", \"false\");\nlocalStorage.setItem(\"user_mode\", \"null\");\nlocalStorage.setItem(\"checked\", \"null\");\nlocalStorage.setItem(\"user-icon\", \"null\");</script>";
    }
    else{
        $logged_user = $_SESSION['user-logged'];
        $mode = $_SESSION['mode'];
        $checked = $_SESSION['checked'];
        $img = $_SESSION['user-icon'];
        echo "<script>\nlocalStorage.setItem(\"logged-user\", \"$logged_user\");\nlocalStorage.setItem(\"user_mode\", \"$mode\");\nlocalStorage.setItem(\"checked\", \"$checked\");\nlocalStorage.setItem(\"user-icon\", \"$img\");\n</script>";
        unset($logged_user);
        unset($mode);
        unset($checked);
    }
}

/**
 * Creates a signature card object. That card contains the main data about a signature, the main showed data are:
 *     * The signature ID (Database PK)
 *     * The algo/hash that will be encoded (md5, sha1, sha256 etc)
 *
 * @param int $signature_id
 * @param string $algo The choosed hash
 * @param string|null $opts_link if will have a link to the management of the signature (only for proprietaries)
 * @param string $proprietary_nm The proprietary of the signature
 * @return void
 */
function createSignatureCard(int $signature_id, string $algo, $opts_link, string $proprietary_nm, string $dt_creation){
    $obj = "<div class=\"signature-card\">\n";
    $obj_signature_name = is_null($opts_link) ? "<div class=\"signature-name\">Signature #" . $signature_id . "</div>" : "<div class=\"signature-name\"><a href=\"$opts_link\">Signature #$signature_id</a></div>";
    $obj_prop_nm = "<div class=\"proprietary-ref\">Proprietary: $proprietary_nm</div>";
    $obj_dt = "<div class=\"dt-signature\">Date & time created: $dt_creation</div>";
    $obj_algo = "<div class=\"choosed-hash\">Hashed at: $algo</div>";
    $obj .= $obj_signature_name . "\n" . $obj_prop_nm . "\n" . $obj_algo . "\n" . $obj_dt;
    echo $obj;
}

/**
 * That method sets all the signatures from a proprietary
 * @param int $proprietary The primary key reference of the proprietary to get him signatures
 * @return void
 */
function lsSignaturesMA(int $proprietary){
    $all = "";
    $sig = new SignaturesData("giulliano_php", "");
    $signatures = $sig->qrSignatureProprietary($proprietary);
    if(is_null($signatures)){ return "<h1>You don't have any signature yet!</h1>";}
    foreach($signatures as $cd){
        $sig_data = $sig->getSignatureData($cd);
        // TODO Upgrade the layout of the signature card
        $card = "<div class=\"card signature-card\">\n<div class=\"card-body\">\n<h3 class=\"card-title\"> Signature #$cd</h3>\n<h5 class=\"card-subtitle\">" . $sig_data['dt_creation'] . "\n</h5><div class=\"card-text\"><a href=\"https://localhost/lpgp-server/cgi-actions/get_my_signature.php?id=$cd\">Download</a><br><a href=\"https://localhost/lpgp-server/cgi-actions/signature_config.php?id=$cd\">Configurations</a>\n</div>\n</div><br>";
        $all .= "\n$card\n";
    }
    return $all;
}

/**
 * Do the same thing then the lsSignaturesMA, but from a different proprietary and without the Download & Configurations options in the 
 * Signature card.
 *
 * @param integer $proprietary The primary key reference of the other proprietary
 * @return string
 */
function lsExtSignatures(int $proprietary){
    $all = "";
    $sig = new SignaturesData("giulliano_php", "");
    $signatures = $sig->qrSignatureProprietary($proprietary);
    if(is_null($signatures)){ return "<h1>You don't have any signature yet!</h1>";}
    foreach($signatures as $cd){
        $sig_data = $sig->getSignatureData($cd);
        // TODO Upgrade the layout of the signature card
        $card = "<div class=\"card signature-card\">\n<div class=\"card-body\">\n<h3 class=\"card-title\"> Signature #$cd</h3>\n<h5 class=\"card-subtitle\">" . $sig_data['dt_creation'] . "\n</h5></div>";
        $all .= "\n$card\n";
    }
    return $all;
}

/**
 * Returns a valid path to the image file of any user/proprietary.
 *
 * @param string $raw_path The raw path of the image.
 * @param bool $ext_root If the script that's calling the method is in the server root.
 * @return string
 */
function getImgPath(string $raw_path, bool $ext_root = true){
    $exp = explode("/", $raw_path);
    return $ext_root ? "../" . $exp[count($exp) - 2] . "/" . $exp[count($exp) - 1] : "./" . $exp[count($exp) - 2] . "/" . $exp[count($exp) - 1];
}

/**
 * That method sets all the values of a signature, it is used for the configurations of the signature, at the file signature_config.php
 * @param integer $signature The Primary key reference of the signature at the database.
 * @return string The string with all the inputs of the signature data.
 */
function inputsGets(int $signature){
    $sign = new SignaturesData("giulliano_php", "");
    $dt = $sign->getSignatureData($signature);
    $main_str = "<h1>Signature #" . $dt['cd_signature'] . "</h1>\n";
    $passwd = $dt['vl_password'];
    $main_str .= "<input value=\"$passwd\" name=\"vl-passwd\" class=\"form-control\" label=\"The raw signature\">\n";
    $main_str .= $sign->getCodesHTML(true, (int)$dt['vl_code']) . "\n";
    return $main_str;
}
?>