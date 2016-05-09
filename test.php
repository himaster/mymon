<?php
require_once 'functions.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE);

$docroot  = dirname(__FILE__);
$serverip = "pkwteile.no-ip.biz";
$connection = ssh2_connect($serverip, 22);
ssh2_auth_pubkey_file($connection, 'root', $docroot.'/id_rsa.pub', $docroot.'/id_rsa', '');
$str = ssh2_return($connection, "printf %s \"$(mysql -e 'show slave status\G' | awk 'FNR>1')\"");

foreach (explode("\n", $str) as $cLine) {
    if (strpos($cLine, "Timeout") != false) {
        echo "<font color=\"red\">".strpos($cLine, "Timeout")." - stopped</font>";
    }
    list($cKey, $cValue) = explode(':', $cLine, 2);
    $data[trim($cKey)] = trim($cValue);
}
