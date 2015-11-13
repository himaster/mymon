<?php

require_once "functions.php";
$serverip = "94.242.254.35";
$servername = "vpnout";

$i = 1;
$ssh_conname = "ssh_".$servername;
start:
if ((!$$ssh_conname = ssh2_connect($serverip, 22)) or (!ssh2_auth_pubkey_file($$ssh_conname, 'root', '/root/.ssh/id_rsa.pub', '/root/.ssh/id_rsa', ''))) {
	common_log($servername." - retry #".$i++.".");
	sleep(1);
	goto start;
}
$mysql_conname = "mysql_".$servername;
$$mysql_conname = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($$mysql_conname->connect_errno."\n");
$result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `timestamp`=`CURRENT_TIMESTAMP` WHERE `ip`='" .$serverip. "';");
$result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `la`='TEST' WHERE `ip`='" .$serverip. "';");

if (!isset($result)) die($servername." - LA not updated!");
unset($result);

unset($$ssh_conname);

exit;

function la($connection, $serverip) {
	global $servername;
	global $hostname;
	$la = substr(strrchr(ssh2_return($connection, "/usr/bin/uptime"),":"),1);
	$la1 = intval(array_map("trim",explode(",",$la))[0]);
	$core = ssh2_return($connection, "grep -c processor /proc/cpuinfo");
	if ($la1 < ($core/2)) $fontcolor = "<font color=\"green\">";
	elseif (($la1 >= ($core/2)) && ($la1 < ($core * 0.75))) $fontcolor = "<font color=\"#CAC003\">";
	else $fontcolor = "<font color=\"red\">";
	#return "<a title=\"Click to show processes\" 
	#		   href=\"https://" .$hostname. "/index.php?task=top&serverip=" .$serverip. "\"
	#		   target=\"_blank\">" .$fontcolor. "<b>" .$la. "</b></font>\n</a>";
	return "TEST";
}