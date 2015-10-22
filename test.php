<?php

include "functions.php";
$serverip = "136.243.42.200";
$connection = ssh2_connect($serverip, 22);
if (! ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
	die("<font color=\"red\">* * *</font>");
}

$str = ssh2_return($connection, "mysql -e 'show slave status\G'");

print_r($str);
$sql = substr(strstr($str, 'Slave_SQL_Running:'), 19, 3);
$sql = trim(preg_replace('/\s+/', ' ', $sql));

$io = substr(strstr($str, 'Slave_IO_Running:'), 18, 3);
$io = trim(preg_replace('/\s+/', ' ', $io));

$delta = substr(strstr($str, 'Seconds_Behind_Master:'), 23, 2);
$delta = trim(preg_replace('/\s+/', ' ', $delta));

if ($sql == "Yes") $sqlfontcolor = "<font color=\"green\">";
else $sqlfontcolor = "<font color=\"red\">";

if ($io == "Yes") $iofontcolor = "<font color=\"green\">";
else $iofontcolor = "<font color=\"red\">";

if ($delta == 0) $deltafontcolor = "<font color=\"green\">";
else $deltafontcolor = "<font color=\"red\">";

unset($connection);
echo "<a title=\"Click to restart replication\" 
    	 href=\"#\" 
    	 onclick=\"myAjax(\'" .$serverip. "\')\">
    	 SQL: " .$sqlfontcolor. "<b>" .$sql. "</b></font> 
    	 IO: " .$iofontcolor. "<b>" .$io. "</b></font> 
    	 Δ: " .$deltafontcolor. "<b>" .$delta. "</b></font>\n</a>";