<?php
if ($_SERVER['HTTP_HOST'] != "mymon.pkwteile.de") {
	header("Location: https://mymon.pkwteile.de/");
}

if ($_GET['task'] == "exit") {
	setcookie('mymon[login]', '', time()-604800, dirname($_SERVER['PHP_SELF']), $_SERVER['HTTP_HOST'], isset($_SERVER["HTTP_X_FORWARDED_PROTOCOL"]), true);
	setcookie('mymon[password]', '', time()-604800, dirname($_SERVER['PHP_SELF']), $_SERVER['HTTP_HOST'], isset($_SERVER["HTTP_X_FORWARDED_PROTOCOL"]), true);
	unset($_COOKIE['mymon']);
	header("Location: https://" .$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
	die();
}

include_once("functions.php");
include_once("connect.php");

if (isset($_COOKIE["mymon"])) {
 	$login = no_injection($_COOKIE["mymon"]["login"]);
	$password = no_injection($_COOKIE["mymon"]["password"]);
	$query = "SELECT id, login, password, email FROM users WHERE login ='{$login}' AND password='{$password}' AND approvied='1' LIMIT 1";
	$sql = mysql_query($query) or die(mysql_error());
	if (mysql_num_rows($sql) == 1) {
		if (isset($_GET["serverip"])) {
			$connection = ssh2_connect($_GET["serverip"], 22);
			if (! ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
   				die("<font color=\"red\">* * *</font>");
			}
		}
		switch ($_GET["task"]) {
			case "500err":
				if (!isset($_GET['serverip'])){
   					die('Server is not defined!');
				}
				include "header.html";
				echo("<div class=\"back_menu\">");
				echo("<a href=\"#\" onclick=\"self.close()\">");
				echo("<img src=\"./images/back.png\"></a>");
				echo("</div><div class=\"textstyle\">");
				$stream1 = ssh2_exec($connection, "cat /var/log/500.errs");
				stream_set_blocking($stream1, true);
				$str = stream_get_contents($stream1);
				echo nl2br($str);
				echo "</div>";
				include "footer.html";
				break;

			case "editor":
				$file = '/var/www/netbox.co/mymon/servers.conf';
				$mass = file($file);
				$text = file_get_contents($file);
				if (isset($_POST['text'])) {
					// save the text contents
					file_put_contents($file, $_POST['text']);
					// redirect to form again
					header("Location: https://" .$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
					printf("<a href=\"%s\">Moved</a>.", htmlspecialchars($url));
					exit();
				}
				include "header.html";
				include "editor.php";		
				include "footer.html";
				break;

			case "replica":
				if (!isset($_GET['serverip'])){
   					die('Server is not defined!');
				}
			    $backin = array("88.198.182.132","88.198.182.134","88.198.182.146");
			    if (in_array($_GET['serverip'], $backin)){
			    	$masterip = "88.198.182.130";
				    $connection_master = ssh2_connect($masterip, 22);
					if (! ssh2_auth_pubkey_file($connection_master, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
	   					die("<font color=\"red\">Connection to master failed!</font>");
					}
				    $stream = ssh2_exec($connection_master, "mysql -N -e 'show master status;' | awk '{print $1}'");
				    stream_set_blocking($stream, true);
				    $file = stream_get_contents($stream);
				    $file = trim(preg_replace('/\s+/', ' ', $file));
				    $stream = ssh2_exec($connection_master, "mysql -N -e 'show master status;' | awk '{print $2}'");
				    stream_set_blocking($stream, true);
				    $position = stream_get_contents($stream);
				    unset($connection_master);
					$query = "CHANGE MASTER TO MASTER_HOST=\"10.0.0.1\",
											   MASTER_USER=\"replication\",
											   MASTER_PASSWORD=\"ZsppM0H9q1hcKTok7O51\",
											   MASTER_LOG_FILE=\"" .$file. "\",
											   MASTER_LOG_POS=" .$position. ";";
			    } elseif ($_GET['serverip'] == "188.138.33.212") {
			    	$masterip = "88.198.182.130";
				    $connection_master = ssh2_connect($masterip, 22);
					if (! ssh2_auth_pubkey_file($connection_master, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
	   					die("<font color=\"red\">Connection to master failed!</font>");
					}
				    $stream = ssh2_exec($connection_master, "mysql -N -e 'show master status;' | awk '{print $1}'");
				    stream_set_blocking($stream, true);
				    $file = stream_get_contents($stream);
				    $file = trim(preg_replace('/\s+/', ' ', $file));
				    $stream = ssh2_exec($connection_master, "mysql -N -e 'show master status;' | awk '{print $2}'");
				    stream_set_blocking($stream, true);
				    $position = stream_get_contents($stream);
				    unset($connection_master);
					$query = "CHANGE MASTER TO MASTER_HOST=\"88.198.182.130\",
											   MASTER_USER=\"replication\",
											   MASTER_PASSWORD=\"ZsppM0H9q1hcKTok7O51\",
											   MASTER_LOG_FILE=\"".$file."\",
											   MASTER_LOG_POS=" . $position . ";";
			    } elseif ($_GET['serverip'] == "136.243.42.200") {
			    	$masterip = "136.243.43.35";
				    $connection_master = ssh2_connect($masterip, 22);
					if (! ssh2_auth_pubkey_file($connection_master, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
	   					die("<font color=\"red\">Connection to master failed!</font>");
					}
				    $stream = ssh2_exec($connection_master, "mysql -N -e 'show master status;' | awk '{print $1}'");
				    stream_set_blocking($stream, true);
				    $file = stream_get_contents($stream);
				    $file = trim(preg_replace('/\s+/', ' ', $file));
				    $stream = ssh2_exec($connection_master, "mysql -N -e 'show master status;' | awk '{print $2}'");
				    stream_set_blocking($stream, true);
				    $position = stream_get_contents($stream);
				    unset($connection_master);
				    $query = "CHANGE MASTER TO MASTER_HOST=\"10.0.0.2\",
				    						   MASTER_USER=\"replication\",
				    						   MASTER_PASSWORD=\"ZsppM0H9q1hcKTok7O51\",
				    						   MASTER_LOG_FILE=\"" .$file. "\",
				    						   MASTER_LOG_POS=" .$position. ";";
			    }
			    ssh2_exec($connection, "mysql -N -e 'stop slave;'");
			    if (!empty($query)) {
			    	ssh2_exec($connection, "mysql -N -e '$query' 2>&1");
			    }
			    ssh2_exec($connection, "mysql -N -e 'start slave;'");
			    unset($connection);
			    break;

			case "top":
				if (!isset($_GET['serverip'])){
   					die('Server is not defined!');
				}
				header("Refresh: 5");
				include "header.html";
				echo "<div class=\"back_menu\">";
				echo "<a href=\"#\" onclick=\"self.close()\"><img src=\"images/back.png\"></a>";
				echo "</div><div class=\"textstyle\">";
				$stream = ssh2_exec($connection, "ps aux --sort=-pcpu | head -n 30");
				stream_set_blocking($stream, true);
				$str = stream_get_contents($stream);
				$str = nl2br($str);
				echo($str);
				echo "</div>";
				include "footer.html";
				break;

			case 'la':
				$query = "SELECT la FROM stats WHERE ip=\"{$_GET['serverip']}\" LIMIT 1";
				$result = mysql_query($query) or die(mysql_error());
				$row = mysql_fetch_assoc($result);
				echo $row["la"];
				break;

			case 'rep':
				$query = "SELECT rep FROM stats WHERE ip=\"{$_GET['serverip']}\" LIMIT 1";
				$result = mysql_query($query) or die(mysql_error());
				$row = mysql_fetch_assoc($result);
				echo $row["rep"];
				break;

			case '500':
				$query = "SELECT `500` FROM stats WHERE ip=\"{$_GET['serverip']}\" LIMIT 1";
				$result = mysql_query($query) or die(mysql_error());
				$row = mysql_fetch_assoc($result);
				echo $row["500"];
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
					echo $output;
				} else {
					echo "<font color='red'>Timeout</font>";
				}
				break;

			case "confirm":
				$login = no_injection($_GET["username"]);
				$query = "UPDATE users SET approvied = '1' WHERE login = '$login'";
				$result = mysql_query($query) or die(mysql_error());
				$query = "SELECT email FROM users WHERE login = '$login'";
				$result = mysql_query($query) or die(mysql_error());
				$msg = "Hi! Your login ($login) just confirmed. Try to login on https://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
				$msg = wordwrap($msg,70);
				$headers =  "From: mymon@netbox.co\r\nReply-To: himaster@mailer.ag\r\n";
				mail(mysql_fetch_assoc($result)['email'],"Mymon registration",$msg,$headers);
				echo "<p>Профиль успешно обновлен";
				break;

			default:
				setcookie('mymon[login]', $login, time()+604800, dirname($_SERVER['PHP_SELF']), $_SERVER['HTTP_HOST'], isset($_SERVER["HTTP_X_FORWARDED_PROTOCOL"]), true);
				setcookie('mymon[password]', $password, time()+604800, dirname($_SERVER['PHP_SELF']), $_SERVER['HTTP_HOST'], isset($_SERVER["HTTP_X_FORWARDED_PROTOCOL"]), true);
				include "header.html";
		    	include "table.php";
		    	include "footer.html";
		    	break;
		}
		if (isset($connection)) {
			unset($connection);
		}		
    }
	else
		echo 'Неправильное имя или пароль в куках???';
} 
elseif(isset($_POST['auth_submit'])) {
	$login = no_injection($_POST['login']);
	$password = md5(no_injection($_POST['password']));
	$query = "SELECT id, login, password, email FROM users WHERE login ='{$login}' AND password='{$password}' AND approvied='1' LIMIT 1";
	$sql = mysql_query($query) or die(mysql_error());
	if (mysql_num_rows($sql) == 1) {
		setcookie('mymon[login]', $login, time()+604800, dirname($_SERVER['PHP_SELF']), $_SERVER['HTTP_HOST'], isset($_SERVER["HTTP_X_FORWARDED_PROTOCOL"]), true);
		setcookie('mymon[password]', $password, time()+604800, dirname($_SERVER['PHP_SELF']), $_SERVER['HTTP_HOST'], isset($_SERVER["HTTP_X_FORWARDED_PROTOCOL"]), true);
		include "header.html";
    	include "table.php";
    	include "footer.html";
    }
	else
		echo 'Неправильное имя или пароль';
} 
else {
	include "auth.php";
}
?>