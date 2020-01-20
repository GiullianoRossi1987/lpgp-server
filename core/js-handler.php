<?php
namespace JSHandler;
if(session_status() == PHP_SESSION_NONE)session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";

// TODO Create a modal method. Just for the UX

use Core\ProprietariesData;
use Core\SignaturesData;
use ProprietariesExceptions\ProprietaryNotFound;

/**
 * That method sends the $_SESSION vars about the logged user to the localStorage. In a inexisting session
 * It will set such as no one logged at the system.
 *
 * @return void
 */
function sendUserLogged(){
    if(session_status() == PHP_SESSION_NONE) session_start();
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
        $card = "<div class=\"card signature-card\">\n<div class=\"card-body\">\n<h3 class=\"card-title\"> Signature #$cd</h3>\n<h5 class=\"card-subtitle\">" . $sig_data['dt_creation'] . "\n</h5><div class=\"card-text\"><a href=\"https://localhost/lpgp-server/cgi-actions/get_my_signature.php?id=$cd\">Download</a><br><a href=\"https://localhost/lpgp-server/cgi-actions/ch_signature_data.php?sig_id=$cd\">Configurations</a>\n</div>\n</div><br>";
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

/**
 * Returns the HTML code of a signature card after the validation.
 * @param integer $sign_ref The primary key reference of the signature to create the card.
 * @param bool $valid If the signature is valid, used after the authentication.
 * @return string The HTML code. 
 */
function createSignatureCardAuth(int $sign_ref, bool $valid){
    $sign_obj = new SignaturesData("giulliano_php", "");
    $data = $sign_obj->getSignatureData($sign_ref);
    $prp_obj = new ProprietariesData("giulliano_php", "");
    try{
        $prop_nm = $prp_obj->getPropDataByID($data['id_proprietary'])['nm_proprietary'];
    }
    catch(ProprietaryNotFound $e){
        $prop_nm = "(Proprietary not found)";
    }
    $card_str = "<div class=\"card signature-vl-card\">\n";
    if($valid){
        $card_str .= "<div class=\"card-header\">\n";
        $card_str .= "<span class=\"span-card-vl\">\n<img src=\"https://localhost/lpgp-server/media/checked-valid.png\" width=\"50px\" height=\"50px\">\n</span>\n";
        $card_str .= "<h1 class=\"card-title\">Signature #$sign_ref</h1>\n";
        $card_str .= "</div>\n";
        // end of the header (card)
        // start of the body (card)
        $card_str .= "<div class=\"card-body\">\n";
        $card_str .= "<h3>Proprietary: ";
        $id = $data['id_proprietary'];
        $prp_a = "<a href=\"https://localhost/lpgp-server/cgi-actions/proprietary.php?id=$id\" target=\"_blanck\">$prop_nm</a>";
        $card_str .= $prp_a . "</h3>\n";
        unset($prp_a);
        $card_str .= "<h3>Created in: " . $data['dt_creation'] . "</h3>\n";
        $card_str .= "</div>";
        // end of the body
        // start of the footer
        $card_str .= "<div class=\"card-footer\"></div>";
        // end of the card
        $card_str .= "</div>";
    }
    else{
        $card_str .= "<div class=\"card-header\">\n";
        $card_str .= "<span class=\"span-card-vl\">\n<img src=\"https://localhost/lpgp-server/media/checked-invalid.png\" width=\"50px\" height=\"50px\">\n</span>\n";
        $card_str .= "<h1 class=\"card-title\">Signature #$sign_ref</h1>\n";
        $card_str .= "</div>\n";
        // end of the header (card)
        // start of the body (card)
        $card_str .= "<div class=\"card-body\">\n";
        $card_str .= "<h3>Proprietary: ";
        $id = $data['id_proprietary'];
        $prp_a = "<a href=\"https://localhost/lpgp-server/cgi-actions/proprietary.php?id=$id\" target=\"_blanck\">$prop_nm</a>";
        $card_str .= $prp_a . "</h3>\n";
        unset($prp_a);
        $card_str .= "<h3>Created in: " . $data['dt_creation'] . "</h3>\n";
        $card_str .= "</div>";
        // end of the body
        // start of the footer
        $card_str .= "<div class=\"card-footer\"></div>";
        // end of the card
        $card_str .= "</div>";
    }
    return $card_str;
}
?>