<?php

include "functions.php";

function rep($connection, $serverip) {
	$data = array();
	$str = ssh2_return($connection, "mysql -e 'show slave status\G'");
	foreach (explode("\n", $str) as $cLine) {
		list ($cKey, $cValue) = explode(':', $cLine, 2);
		$data[trim($cKey)] = trim($cValue);
	}
    if ($data["Slave_SQL_Running"] == "Yes") {
    	$sqlfontcolor = "<font color=\"green\">";
    	$sql = "&#10003;";
    } else {
    	$sqlfontcolor = "<font color=\"red\">";
    	$sql = "x<script type='javascript'>notify('Replication IO problem');</script>";
    }
    if ($data["Slave_IO_Running"] == "Yes") {
    	$iofontcolor = "<font color=\"green\">";
    	$io = "&#10003;";
    } else {
    	$iofontcolor = "<font color=\"red\">";
    	$io = "x<script type='javascript'>notify('Replication IO problem');</script>";
    }
    if ($data["Seconds_Behind_Master"] == "0") $deltafontcolor = "<font color=\"green\">";
    else $deltafontcolor = "<font color=\"red\">";
    return "<a title=\"Click to restart replication\" 
    		   href=\"#\" 
    		   onclick=\"javascript: if(confirm(\'Want to restart replication?\')) myAjax(\'" .$serverip. "\'); \">
    		   SQL: " .$sqlfontcolor. "<b>" .$sql. "</b></font> 
    		   IO: " .$iofontcolor. "<b>" .$io. "</b></font> 
    		   &#916;: " .$deltafontcolor. "<b>" .$data["Seconds_Behind_Master"]. "</b></font>\n</a>";
}

$servername = "backend4";
$serverip = "88.198.182.146";
$ssh_conname = "ssh_".$servername;
start:
$$ssh_conname = ssh2_connect($serverip, 22);
if ((!$$ssh_conname) or (!ssh2_auth_pubkey_file($$ssh_conname, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', ''))) {
	common_log($servername." - retry #".$i++.".");
	sleep(1);
	if ($i < $retry_num) goto start;
	else exit(1);
}
echo rep($$ssh_conname, $serverip);

if ($db == 1) $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `rep`='".rep($$ssh_conname, $serverip)."' , `timestamp`=CURRENT_TIMESTAMP WHERE `ip`='" .$serverip. "';");
else $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `rep`='' WHERE `ip`='" .$serverip. "';");
if (!isset($result)) common_log($servername." - REP not updated!");
unset($result);
