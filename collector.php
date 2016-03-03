<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
include_once "functions.php";
declare(ticks=1);
set_error_handler('errHandler');
pcntl_signal(SIGTERM, "sigHandler");

$connection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($connection->connect_errno."\n");
$result = $connection->query("SELECT ip, servername, db, mysql, err, el, mon, red FROM `mymon`.`stats`;") or die($connection->error);
#$i = 1;
$connection->close();
while($array = $result->fetch_assoc()) {
    $pid = pcntl_fork();
    if ($pid == -1) die("Child process can't be created");
    elseif ($pid) parent_();
    else {
		child_();
		exit;
    }
}
$result->free();
exit;

function parent_() {}

function child_() {
	global $array;
	global $stop_server;
	global $servername;
	$retry_num = 10;
	common_log($servername. " - started.");
	$serverip = $array["ip"];
	$servername = $array["servername"];
	$errs = $array["err"];
	$elastic = $array["el"];
	$db = $array["db"];
	$mysql = $array["mysql"];
	$mon = $array["mon"];
	$red = $array["red"];
	$i = 1;
	$ssh_conname = "ssh_".$servername;
	start:
	$$ssh_conname = ssh2_connect($serverip, 22);
	if ((!$$ssh_conname) or (!ssh2_auth_pubkey_file($$ssh_conname, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', ''))) {
		common_log($servername." - retry #".$i++.".");
		sleep(1);
		if ($i < $retry_num) goto start;
		else exit(1);
	}
	$mysql_conname = "mysql_".$servername;
	$$mysql_conname = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($$mysql_conname->connect_errno."\n");
	$result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `la`='".la($$ssh_conname, $serverip)."' , `timestamp`=CURRENT_TIMESTAMP WHERE `ip`='" .$serverip. "';");
	if (!isset($result)) common_log($servername." - LA not updated!");
	unset($result);
	if ($db == 1) $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `rep`='".rep($$ssh_conname, $serverip)."' , `timestamp`=CURRENT_TIMESTAMP WHERE `ip`='" .$serverip. "';");
	else $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `rep`='' WHERE `ip`='" .$serverip. "';");
	if (!isset($result)) common_log($servername." - REP not updated!");
	unset($result);
	if ($errs == 1) $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `500`='" .err500($$ssh_conname, $serverip). "' , `timestamp`=CURRENT_TIMESTAMP WHERE `ip`='" .$serverip. "';");
	else $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `500`='' WHERE `ip`='" .$serverip. "';");
	if (!isset($result)) common_log($servername." - 500 not updated!");
	unset($result);
	if ($elastic == 1) $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `elastic`='" .elastic($$ssh_conname, $serverip). "' , `timestamp`=CURRENT_TIMESTAMP WHERE `ip`='" .$serverip. "';");
	else $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `elastic`='' WHERE `ip`='" .$serverip. "';");
	if (!isset($result)) common_log($servername." - ELASTIC not updated!");
	unset($result);
	if ($mysql == 1) $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `locks`='" .locks($$ssh_conname, $serverip). "' , `timestamp`=CURRENT_TIMESTAMP WHERE `ip`='" .$serverip. "';");
	else $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `locks`='' WHERE `ip`='" .$serverip. "';");
	if (!isset($result)) common_log($servername." - LOCKS not updated!");
	unset($result);
	if ($mon == 1) $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `mongo`='" .mongo($$ssh_conname, $serverip). "' , `timestamp`=CURRENT_TIMESTAMP WHERE `ip`='" .$serverip. "';");
	else $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `mongo`='' WHERE `ip`='" .$serverip. "';");
	if (!isset($result)) common_log($servername." - MONGO not updated!");
	unset($result);
	if ($red == 1) $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `redis`='" .redis($$ssh_conname, $serverip). "' , `timestamp`=CURRENT_TIMESTAMP WHERE `ip`='" .$serverip. "';");
	else $result = $$mysql_conname->query("UPDATE `mymon`.`stats` SET `redis`='' WHERE `ip`='" .$serverip. "';");
	if (!isset($result)) common_log($servername." - REDIS not updated!");
	unset($result);
	$$mysql_conname->close();
	unset($$mysql_conname);
	unset($$ssh_conname);
	common_log($servername. " - ended.");
}

function la($connection, $serverip) {
	global $servername;
	global $hostname;
	$la = substr(strrchr(ssh2_return($connection, "/usr/bin/uptime"),":"),1);
	$la1 = intval(array_map("trim",explode(",",$la))[0]);
	$core = ssh2_return($connection, "grep -c processor /proc/cpuinfo");
	if ($la1 < ($core * 0.75)) $fontcolor = "<font color=\"green\">";
	elseif (($la1 >= ($core * 0.75)) && ($la1 < $core)) $fontcolor = "<font color=\"#CAC003\">";
	else $fontcolor = "<font color=\"red\">";

	return "<a title=\"Click to show processes\" 
			   href=\"https://" .$hostname. "/index.php?task=top&serverip=" .$serverip. "\"
			   target=\"_blank\">" .$fontcolor. "<b>" .$la. "</b></font>\n</a>";
}

function rep($connection, $serverip) {
	$data = array();
	$str = ssh2_return($connection, "printf %s \"$(mysql -e 'show slave status\G' | awk 'FNR>1')\"");
	foreach (explode("\n", $str) as $cLine) {
		list($cKey, $cValue) = explode(':', $cLine, 2);
		$data[trim($cKey)] = trim($cValue);
	}
	if (!array_key_exists("Slave_SQL_Running", $data)) {
		return "<font color=\"red\">Mysql stopped</font>";
	}
    if ($data["Slave_SQL_Running"] == "Yes") {
    	$sqlfontcolor = "<font color=\"green\">";
    	$sql = "&#10003;";
    } else {
    	$sqlfontcolor = "<script type=\"text/javascript\">new_mes = notify(\"Replication SQL problem\");setTimeout(new_mes.close.bind(new_mes), 2000);</script><font color=\"red\">";
    	$sql = "x";
    }
    if ($data["Slave_IO_Running"] == "Yes") {
    	$iofontcolor = "<font color=\"green\">";
    	$io = "&#10003;";
    } else {
    	$iofontcolor = "<script type=\"text/javascript\">new_mes = notify(\"Replication IO problem\");setTimeout(new_mes.close.bind(new_mes), 2000);</script><font color=\"red\">";
    	$io = "x";
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

function err500($connection, $serverip) {
	global $servername;
	global $hostname;
	$str = trim(ssh2_return($connection, "cat /var/log/500err.log"));

    return "<a title=\"Click to show 500 errors\" 
    		 href=https://". $hostname. "/index.php?task=500err&serverip=" .$serverip. " 
    		 target=\"_blank\">" .$str. "\n</a>";
}

function elastic($connection, $serverip) {
	$str = ssh2_return($connection, "date1=\$((\$(date +'%s%N') / 1000000));
									 curl -sS -o /dev/null -XGET http://`ip -f inet addr show eth1 | grep -Po 'inet \K[\d.]+'`:9200/_cluster/health?pretty;
									 date2=\$((\$(date +'%s%N') / 1000000));
									 echo -n \$((\$date2-\$date1));");
	if ( $str == "Timeout" ) $fontcolor = "<script type=\"text/javascript\">new_mes = notify(\"Elastic problem\");setTimeout(new_mes.close.bind(new_mes), 2000);</script><font color=\"red\">";
	else $fontcolor = "<font color=\"green\">";

	return $fontcolor.$str. "<font size=\"1\"> ms</font></font>";
}

function locks($connection, $serverip) {
	$locked = trim(ssh2_return($connection, "mysql -Ne \"SELECT info FROM INFORMATION_SCHEMA.PROCESSLIST WHERE state LIKE '%lock%' AND time > 30\" | wc -l"));
	$conns  = trim(ssh2_return($connection, "mysql -Nse \"SHOW STATUS WHERE variable_name = 'Threads_connected'\" | awk '{print $2}'"));
    if (($locked == "0") and ($conns < "1000")) $fontcolor = "<font color=\"green\">";
    else $fontcolor = "<script type=\"text/javascript\">new_mes = notify(\"DB locks\");setTimeout(new_mes.close.bind(new_mes), 2000);</script><font color=\"red\">";

    return $fontcolor.$conns. " / " .$locked. "</font>";
}

function mongo($connection, $serverip) {
	$str = ssh2_return($connection, "date1=\$((\$(date +'%s%N') / 1000000));
									 mongo admin --quiet --eval 'printjson(db.serverStatus().connections.current)' 1>/dev/null;
									 date2=\$((\$(date +'%s%N') / 1000000));
									 echo -n \$((\$date2-\$date1));");
	if ( $str == "Timeout" ) $fontcolor = "<script type=\"text/javascript\">new_mes = notify(\"Mongo problem\");setTimeout(new_mes.close.bind(new_mes), 2000);</script><font color=\"red\">";
	else $fontcolor = "<font color=\"green\">";

	return $fontcolor.$str. "<font size=\"1\"> ms</font></font>";
}

function redis($connection, $serverip) {
	$str = ssh2_return($connection, "date1=\$((\$(date +'%s%N') / 1000000));
									 redis-cli info 1>/dev/null;
									 date2=\$((\$(date +'%s%N') / 1000000));
									 echo -n \$((\$date2-\$date1));");
	if ( $str == "Timeout" ) $fontcolor = "<script type=\"text/javascript\">new_mes = notify(\"Redis problem\");setTimeout(new_mes.close.bind(new_mes), 2000);</script><font color=\"red\">";
	else $fontcolor = "<font color=\"green\">";

	return $fontcolor.$str. "<font size=\"1\"> ms</font></font>";
}

function sigHandler($signo) {
	global $stop_server;
	global $connection;
	switch($signo) {
		case SIGTERM: {
			$stop_server = true;
			common_log("SIGTERM stop");
			break;
		}
		case SIGPIPE: {
			$stop_server = true;
			common_log("SIGPIPE stop");
			break;
		}
		default: {
			break;
		}
	}
}

function errHandler($errno, $errmsg, $filename, $linenum) {
	global $servername;
	$date = date('Y-m-d H:i:s (T)');
	$f = fopen('/var/log/mymon/errors.txt', 'a');
	if (!empty($f)) {
		$filename  = str_replace($_SERVER['DOCUMENT_ROOT'],'',$filename);
		fwrite($f, "$date: server: $servername: $errmsg - $filename - $linenum\r\n");
		fclose($f);
	}
}
