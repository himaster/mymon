<?php
if (isset($_GET['serverip']) && isset($_GET['task'])){
	$serverip = $_GET['serverip'];
    $task = $_GET['task'];
	$connection = ssh2_connect($serverip, 22);
	if (! ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
		echo "<center><font color='red'>* * *</font></center>";
		unset($connection);
		die();
	}
	switch ($task) {
		case 'la':
			$stream = ssh2_exec($connection, "/usr/bin/uptime");
			stream_set_blocking($stream, true);
			$str = stream_get_contents($stream);
			$la = substr(strstr($str, 'average:'), 9, strlen($str));
			$la2 = substr($la, 0, strpos($la, ','));
			$la1 = intval($la2);
			$stream = ssh2_exec($connection, "grep -c processor /proc/cpuinfo");
			stream_set_blocking($stream, true);
			$core = stream_get_contents($stream);
			echo "<a href=http://" .$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']). "/top.php?serverip=" .$serverip. " style='text-decoration: none;' target='_blank'>";
			if ($la1 < ($core/2)) {
				echo "<font color='green'>";
			} elseif (($la1 >= ($core/2)) && ($la1 < ($core * 0.75))) {
				echo "<font color='#CAC003'>";
			} else {
				echo "<font color='red'>";
			}
			echo "<b>" .$la. "</b></font></a>";
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
            echo "<b><a href=http://". $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']). "/index.php?task=500err&serverip=" .$serverip. " style='text-decoration: none;' target='_blank'><font color='black'>" .$str. "</font></a></b>";
			break;
		case 'elastic':
			$curTime = microtime(true);
			$stream = ssh2_exec($connection, "date1=\$((\$(date +'%s%N') / 1000000));
							curl -sS -o /dev/null -XGET http://`/sbin/ifconfig eth1 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'`:9200/_cluster/health?pretty;
							date2=\$((\$(date +'%s%N') / 1000000));
    							delta=\$((\$date2-\$date1));
    							echo \$delta;");
			$error_stream = ssh2_fetch_stream( $stream, SSH2_STREAM_STDERR );
			stream_set_blocking( $error_stream, TRUE );
			stream_set_blocking( $stream, TRUE );
			$error_output = stream_get_contents( $error_stream );
			$output = stream_get_contents( $stream );
			if (empty($error_output)) {
				$timeConsumed = round(microtime(true) - $curTime,3)*1000; 
				echo "<b><font color='black'>" .$output. " ms</font></b>";
			} else {
				echo "<b><font color='red'>Timeout</font></b>";
			}
			break;
		default:
			echo "Wrong task";
	}
	unset($connection);
} else {
	echo "Not enough params";
}
?>
