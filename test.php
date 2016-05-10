<?php
require_once 'functions.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE);

$docroot  = dirname(__FILE__);
$serverip = "pkwteile.no-ip.biz";
$connection = new mysqli('188.138.234.38', 'mymon', 'eiGo7iek', 'mymon')
            or die($connection->connect_errno."\n");
$data = array();
$servername = "gw";
$serverip = "188.138.234.38";
$ssh_conname = "ssh_".$servername;
if (( ! $$ssh_conname = @ssh2_connect($serverip, 22))
        or ( ! @ssh2_auth_pubkey_file($$ssh_conname, 'root', $docroot.'/id_rsa.pub', $docroot.'/id_rsa', ''))) {
    exit(1);
}
$str = ssh2_return($$ssh_conname, "printf %s \"$(mysql -e 'show slave status\G' | awk 'FNR>1')\"");
var_dump($str);
die();
foreach (explode("\n", $str) as $cLine) {
    if (strpos($cLine, "Timeout") != false) {
        return "<font color=\"red\">".strpos($cLine, "Timeout")." - stopped</font>";
    }
    list($cKey, $cValue) = explode(':', "$cLine:");
    $data[trim($cKey)] = trim($cValue);
}

var_dump($data["Last_SQL_Error"]);
