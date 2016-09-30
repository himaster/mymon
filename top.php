<?php

if ($_SERVER["SCRIPT_NAME"] != "/index.php") {
    die();
}

if (!$connection = ssh2_connect($_GET["serverip"], 22)) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 501 Internal Server Error', true, 500);
    die();
}
    ssh2_auth_pubkey_file($connection, 'root', $docroot.'/id_rsa.pub', $docroot.'/id_rsa', '');
?>
    <a href="javascript:window.close();">
        <div class="left_button" id="back_button">
            <img src="images/back.png">
        </div>
    </a>
    <div class="textstyle">
<?php
    $str = ssh2_return($connection, "ps aux --sort=-pcpu | head -n 30");
    $str = nl2br($str);
    echo($str);
?>
    </div>
