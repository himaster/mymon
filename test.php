<?php
require_once 'functions.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE);

$docroot  = dirname(__FILE__);
$serverip = "pkwteile.no-ip.biz";
$connection = new mysqli('188.138.234.38', 'mymon', 'eiGo7iek', 'mymon')
            or die($connection->connect_errno."\n");
$data = array();
$str = ssh2_return($connection, "printf %s \"$(mysql -e 'show slave status\G' | awk 'FNR>1')\"");

foreach (explode("\n", $str) as $cLine) {
    if (strpos($cLine, "Timeout") != false) {
        return "<font color=\"red\">".strpos($cLine, "Timeout")." - stopped</font>";
    }
    list($cKey, $cValue) = explode(':', "$cLine:");
    $data[trim($cKey)] = trim($cValue);
}

var_dump($connection->mysqli_real_escape_string($data["Last_SQL_Error"]));
