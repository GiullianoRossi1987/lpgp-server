<?php

namespace JSHandler;

/**
 * That method sends the $_SESSION vars about the logged user to the localStorage. In a inexisting session
 * It will set such as no one logged at the system.
 *
 * @return void
 */
function sendUserLogged(){
    if(session_status() == PHP_SESSION_NONE || session_status() == PHP_SESSION_DISABLED){
        // if there's no one logged.
        echo "<script>\nlocalStorage.setItem(\"logged-user\", \"false\");\nlocalStorage.setItem(\"user_mode\", \"null\");\nlocalStorage.setItem(\"checked\", \"null\");\n</script>";
    }
    else{
        $logged_user = $_SESSION['user-logged'];
        $mode = $_SESSION['mode'];
        $checked = $_SESSION['checked'];
        echo "<script>\nlocalStorage.setItem(\"logged-user\", \"$logged_user\");\nlocalStorage.setItem(\"user_mode\", \"$mode\");\nlocalStorage.setItem(\"checked\", \"$checked\");</script>";
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
?>