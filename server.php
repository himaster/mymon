<?php
if (isset($_GET['serverip']) && isset($_GET['task'])){
	$serverip = $_GET['serverip'];
        $task = $_GET['task'];
        $servername = "localhost";
        $username = "mymon";
        $password = "chai7EeJ";

	$conn = mysql_connect($servername, $username, $password);
	if (!$conn) {
                die('Ошибка соединения: ' . mysql_error());
        }

        $db_selected = mysql_select_db('mymon', $conn);
        if (!$db_selected) {
            die ('Can\'t use foo : ' . mysql_error());
        }

	$connection = ssh2_connect($serverip, 22);
	if (! ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
		die('Public Key Authentication Failed');
	}
	switch ($task) {
		case 'la':
			$stream = ssh2_exec($connection, "/usr/bin/uptime");
			stream_set_blocking($stream, true);
			$str = stream_get_contents($stream);
			$la = substr(strstr($str, 'average:'), 9, strlen($str));
			$la2 = substr($la, 0, strpos($la, ','));
			$la1 = intval($la2);
			$core = 12;
			echo "<a href=http://netbox.co/mymon/top.php?serverip=" .$serverip. " style='text-decoration: none;' target='_blank'>";
			if ($la1 < $core/2) {
				echo "<font color='green'>";
			} elseif ( ($la1 > $core/2) && ($la1 < $core * 0.75) {
				echo "<font color='#CAC003'>";
			} else {
				echo "<font color='red'>";
			}
			echo "<b>" .$la. "</b></font></a>";
			mysql_select_db(mymon, $conn) or die(mysql_error());
			$datetime = date("Y-m-d H:i:s");
			$result = mysql_query("INSERT INTO logs (serverip, datetime, la, lastring) VALUES ('" .$serverip. "', '" .$datetime. "', '" .$la2. "', '" .$la. "');", $conn);
			if (!$result) {
				die('Неверный запрос: ' . mysql_error());
			}
			break;
		case 'rep':
			$stream = ssh2_exec($connection, "mysql -e 'show slave status\G'");
                        stream_set_blocking($stream, true);
                        $str = stream_get_contents($stream);
                        $sql = substr(strstr($str, 'Slave_SQL_Running:'), 19, 3);
                        $io = substr(strstr($str, 'Slave_IO_Running:'), 18, 3);
                        $delta = substr(strstr($str, 'Seconds_Behind_Master:'), 23, 2);
                        echo "<a href='#' onclick=myAjax('" .$serverip. "') style='text-decoration: none;'>";
                        echo "SQL: ";
                        if ($sql == "Yes") {
                                echo "<font color='green'>";
                        } else {
                                echo "<font color='red'>";
                        }
                        echo "<b>" .$sql. "</b>";
                        echo "</font> IO: ";
                        if ($io == "Yes") {
                                echo "<font color='green'>";
                        } else {
                                echo "<font color='red'>";
                        }
                        echo "<b>" .$io. "</b>";
                        echo "</font> Δ: ";
                        if ($delta == 0) {
                                echo "<font color='green'>";
                        } else {
                                echo "<font color='red'>";
                        }
                        echo "<b>" .$delta. "</b></a>";
			break;
		case '500':
			$stream = ssh2_exec($connection, "cat /var/log/500err.log");
                        stream_set_blocking($stream, true);
                        $str = stream_get_contents($stream);
                        echo "<b><a href=http://netbox.co/mymon/500errs.php?serverip=" .$serverip. " style='text-decoration: none;' target='_blank'><font color='black'>" .$str. "</font></a></b>";
			break;
		default:
			echo "Wrong task";
	}
	unset($connection);
} else {
	echo "Not enough params";
}
?>
