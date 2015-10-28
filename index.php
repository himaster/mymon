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
	$return = mysql_query($query) or die(mysql_error());
	if (mysql_num_rows($return) == 1) {
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
				$str = ssh2_return($connection, "cat /var/log/500.errs");
				echo nl2br($str);
				echo "</div>";
				include "footer.html";
				
				break;

			case "editor":
				$file = '/var/www/netbox.co/mymon/servers.conf';
				$mass = file($file);
				$text = file_get_contents($file);
				if (isset($_POST['text'])) {
					file_put_contents($file, $_POST['text']);
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
			    $backout = array("217.118.19.156","pkwteile.no-ip.biz");

			    if (in_array($_GET['serverip'], $backin)){
			    	$masterip = "88.198.182.130";
					$query = "CHANGE MASTER TO MASTER_HOST=\"10.0.0.1\",
											   MASTER_USER=\"replication\",
											   MASTER_PASSWORD=\"ZsppM0H9q1hcKTok7O51\", ";
			    } elseif (in_array($_GET['serverip'], $backout)) {
			    	$masterip = "88.198.182.130";
					$query = "CHANGE MASTER TO MASTER_HOST=\"88.198.182.130\",
											   MASTER_USER=\"replication\",
											   MASTER_PASSWORD=\"ZsppM0H9q1hcKTok7O51\", ";
			    } elseif ($_GET['serverip'] == "136.243.42.200") {
			    	$masterip = "136.243.43.35";
				    $query = "CHANGE MASTER TO MASTER_HOST=\"10.0.0.2\",
				    						   MASTER_USER=\"replication\",
				    						   MASTER_PASSWORD=\"ZsppM0H9q1hcKTok7O51\", ";
			    }

			    $connection_master = ssh2_connect($masterip, 22);
				if (! ssh2_auth_pubkey_file($connection_master, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
   					die("<font color=\"red\">Connection to master failed!</font>");
				}

			    $file = trim(preg_replace('/\s+/', ' ', ssh2_return($connection_master,  "mysql -N -e 'show master status;' | awk '{print $1}'")));
			    $position = ssh2_return($connection_master,  "mysql -N -e 'show master status;' | awk '{print $2}'");
			    $query = $query. "MASTER_LOG_FILE=\"" .$file. "\", MASTER_LOG_POS=" .$position.";";
			    
			    unset($connection_master);
			    
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
				$str = ssh2_return($connection, "ps aux --sort=-pcpu | head -n 30"); 
				$str = nl2br($str);
				echo($str);
				echo "</div>";
				include "footer.html";
				
				break;

			case 'la':
				echo get_data("la", $_GET['serverip']);
				
				break;

			case 'rep':
				echo get_data("rep", $_GET['serverip']);
				
				break;

			case '500':
				echo get_data("500", $_GET['serverip']);
				
				break;

			case 'elastic':
				echo get_data("elastic", $_GET['serverip']);
				
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
	$return = mysql_query($query) or die(mysql_error());
	if (mysql_num_rows($return) == 1) {
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