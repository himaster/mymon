<?php
require_once 'functions.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE);

$connection = new mysqli('188.138.234.38', 'mymon', 'eiGo7iek', 'mymon')
            or die($connection->connect_errno."\n");
$str = ssh2_return($connection, "printf %s \"$(mysql -e 'show slave status\G' | awk 'FNR>1')\"");
foreach (explode("\n", $str) as $cLine) {
    if (strpos($cLine, "Timeout") != false) {
        return "<font color=\"red\">".strpos($cLine, "Timeout")." - stopped</font>";
    }
    list($cKey, $cValue) = explode(':', $cLine, 2);
    $data[trim($cKey)] = trim($cValue);
}
