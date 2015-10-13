<?php

if (isset($_GET["exit"])) {
	unset($_COOKIE["mymon"]);
	setcookie('mymon[login]', '');
	setcookie('mymon[password]', '');
	header("Location: http://" .$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
}

include_once("connect.php");

if (isset($_COOKIE["mymon"])) {
 	$login = $_COOKIE["mymon"]["login"];
	$password = $_COOKIE["mymon"]["password"];
	$query = "SELECT id, login, password FROM users WHERE login ='{$login}' AND password='{$password}' AND approvied='1' LIMIT 1";
	$sql = mysql_query($query) or die(mysql_error());
	if (mysql_num_rows($sql) == 1) {
		#if (isset($_GET["task"])) {
			switch ($_GET["task"]) {
				case "500err":
					if (!isset($_GET['serverip'])){
	   					die('Server is not defined!');
    				}
					include "header.html";
					echo("<div class=\"back_menu\">");
					echo("<a href=\"#\" onclick=\"self.close()\">");
					echo("<img src=\"./images/back.png\"></a>");
					echo("</div><div class=\"err500\">");

					$connection = ssh2_connect($_GET["serverip"], 22);
					if (! ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
   						die('Public Key Authentication Failed');
					}
					$stream1 = ssh2_exec($connection, "cat /var/log/500.errs");
					stream_set_blocking($stream1, true);
					$str = stream_get_contents($stream1);
					echo nl2br($str);
					echo "</div>";
					include "footer.html";
					unset($connection);
					break;

				case "editor":
					$file = '/var/www/netbox.co/mymon/servers.conf';
					$mass = file($file);
					$text = file_get_contents($file);
					if (isset($_POST['text'])) {
    					// save the text contents
    					file_put_contents($file, $_POST['text']);
    					// redirect to form again
    					header("Location: http://" .$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
    					printf("<a href=\"%s\">Moved</a>.", htmlspecialchars($url));
    					exit();
					}
					include "header.html";
					echo "<div class=\"back_menu\">";
					echo "<a href=\"http://" .$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']). "\">";
					echo "<img src=\"images/back.png\"></a>";
					echo "</div>";
					echo "<span align=\"center\">";
					echo "<h2>Server list</h2>";
					echo "<h4>IP&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspname&nbspweb&nbspDB</h4>";
					echo "<form action=\"index.php?task=editor\" method=\"post\">";
					echo "<textarea name=\"text\" cols=\"30\" rows=\"" .count($mass). "\" class=\"editor\">" .htmlspecialchars($text). "</textarea><p>";
					echo "<input type=\"submit\" value=\"Сохранить\" onClick=\"window.location.href='http://" .$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']). "'\" />";
					echo "<input type=\"reset\" />";
					echo "</form></span>";				
					include "footer.html";
					break;

				case "replica":
					if (!isset($_GET['serverip'])){
	   					die('Server is not defined!');
    				}
    				$masterip = "88.198.182.130";
				    $backin = array("88.198.182.132","88.198.182.134","88.198.182.146");

				    $connection = ssh2_connect($masterip, 22);
				    if (! ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
					   die('Public Key Authentication Failed');
				    }
				    $stream = ssh2_exec($connection, "mysql -N -e 'show master status;' | awk '{print $1}'");
				    stream_set_blocking($stream, true);
				    $file = stream_get_contents($stream);
				    $file = trim(preg_replace('/\s+/', ' ', $file));
				    $stream = ssh2_exec($connection, "mysql -N -e 'show master status;' | awk '{print $2}'");
				    stream_set_blocking($stream, true);
				    $position = stream_get_contents($stream);
				    unset($connection);
				    if (in_array($_GET['serverip'], $backin)){
					   $query = "CHANGE MASTER TO MASTER_HOST=\"10.0.0.1\", MASTER_USER=\"replication\", MASTER_PASSWORD=\"ZsppM0H9q1hcKTok7O51\", MASTER_LOG_FILE=\"" .$file. "\", MASTER_LOG_POS=" .$position. ";";
				    } else {
					   $query = "CHANGE MASTER TO MASTER_HOST=\"88.198.182.130\", MASTER_USER=\"replication\", MASTER_PASSWORD=\"ZsppM0H9q1hcKTok7O51\", MASTER_LOG_FILE=\"".$file."\", MASTER_LOG_POS=" . $position . ";";
				    }
				    $connection = ssh2_connect($_GET['serverip'], 22);
				    if (! ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
					   die('Public Key Authentication Failed');
				    }
				    $stream = ssh2_exec($connection, "mysql -N -e 'stop slave;'");
				    $stream = ssh2_exec($connection, "mysql -N -e '$query' 2>&1");
				    ssh2_exec($connection, "mysql -N -e 'start slave;'");
				    unset($connection);
				    break;

				case "top":
					if (!isset($_GET['serverip'])){
	   					die('Server is not defined!');
    				}
    				include "header.html";
					echo "<div class=\"back_menu\">";
					echo "<a href=\"#\" onclick=\"self.close()\"><img src=\"images/back.png\"></a>";
					echo "</div>";
					$serverip = $_GET['serverip'];
					$connection = ssh2_connect($serverip, 22);
					if (! ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
						die('Public Key Authentication Failed');
					}
					$stream = ssh2_exec($connection, "ps aux --sort=-pcpu | head -n 30");
					stream_set_blocking($stream, true);
					$str = stream_get_contents($stream);
					$str = nl2br($str);
					echo($str);
					unset($connection);
					include "footer.html";
					break;

    			default:
    				include "header.html";
			    	include "table.php";
			    	include "footer.html";
			    	break;
			}
		#}		
    }
	else
		echo 'Неправильное имя или пароль в куках???';
} 
elseif(isset($_POST['auth_submit'])) {
	$login = $_POST['login'];
	$password = md5($_POST['password']);
	$query = "SELECT id, login, password FROM users WHERE login ='{$login}' AND password='{$password}' AND approvied='1' LIMIT 1";
	$sql = mysql_query($query) or die(mysql_error());
	if (mysql_num_rows($sql) == 1) {
		setcookie('mymon[login]', $login);
		setcookie('mymon[password]', $password);
		echo "cookie set";
		include "header.html";
    	include "table.php";
    	include "footer.html";
    }
	else
		echo 'Неправильное имя или пароль';
} 
else {
	include "auth.html";
}
?>