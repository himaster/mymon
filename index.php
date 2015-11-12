<?php
error_reporting(E_ALL);
if (($_SERVER['HTTP_HOST'] != "mymon.pkwteile.de") and ($_SERVER['HTTP_HOST'] != "mymon.loc")) header("Location: https://mymon.pkwteile.de/");
if (!isset($_GET['task'])) $_GET['task'] = "NULL";
if ($_GET['task'] == "exit") {
	setcookie('mymon[login]', '', time()-604800, dirname($_SERVER['PHP_SELF']), $_SERVER['HTTP_HOST'], isset($_SERVER["HTTP_X_FORWARDED_PROTOCOL"]), true);
	setcookie('mymon[password]', '', time()-604800, dirname($_SERVER['PHP_SELF']), $_SERVER['HTTP_HOST'], isset($_SERVER["HTTP_X_FORWARDED_PROTOCOL"]), true);
	unset($_COOKIE['mymon']);
	header("Location: https://" .$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
	die();
}

include_once("functions.php");
$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");

if (isset($_COOKIE["mymon"])) {
 	$login = no_injection($_COOKIE["mymon"]["login"]);
	$password = no_injection($_COOKIE["mymon"]["password"]);
	$result = $dbconnection->query("SELECT id, login, password, email FROM `mymon`.`users` WHERE login ='" .$login. "' AND password='" .$password. "' AND approvied='1' LIMIT 1") or die($dbconnection->error);
	if ($result->num_rows == 1) {
		switch ($_GET["task"]) {
			case "500err":
				include "header.html";
				if (!$connection = ssh2_connect($_GET["serverip"], 22)) {
					header($_SERVER['SERVER_PROTOCOL'] . ' 501 Internal Server Error', true, 500);
   					die("Connection error!");
				}
				ssh2_auth_pubkey_file($connection, 'root', 'id_rsa.pub', 'id_rsa', '');
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
				include "header.html";
				include "editor.php";		
				include "footer.html";				
				break;

			case "editor_save":
				$namename = $_GET['name'];
				$pospos = $_GET['pos'];
				$valval = $_GET['val'];
				echo $namename.": ".$pospos." => ".$valval;
				break;

			case "replica":
			    $backin = array("88.198.182.130","88.198.182.132","88.198.182.134","88.198.182.146");
			    $backout = array("217.118.19.156","pkwteile.no-ip.biz");
			    if (in_array($_GET['serverip'], $backin)){
			    	$masterip = "88.198.182.134";
					$query = "CHANGE MASTER TO MASTER_HOST=\"10.0.0.3\", MASTER_USER=\"replication\", MASTER_PASSWORD=\"ZsppM0H9q1hcKTok7O51\", ";
			    } elseif (in_array($_GET['serverip'], $backout)) {
			    	$masterip = "88.198.182.134";
					$query = "CHANGE MASTER TO MASTER_HOST=\"88.198.182.134\", MASTER_USER=\"replication\", MASTER_PASSWORD=\"ZsppM0H9q1hcKTok7O51\", ";
			    } elseif ($_GET['serverip'] == "136.243.42.200") {
			    	$masterip = "136.243.43.35";
				    $query = "CHANGE MASTER TO MASTER_HOST=\"10.0.0.2\", MASTER_USER=\"replication\", MASTER_PASSWORD=\"ZsppM0H9q1hcKTok7O51\", ";
			    }
			    if (!$connection = ssh2_connect($_GET["serverip"], 22)) {
			    	header($_SERVER['SERVER_PROTOCOL'] . ' 501 Internal Server Error', true, 500);
   					die();
			    }
				if (!ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
					die("<font color=\"red\">SSH key for {$_GET["serverip"]} not feat!</font>");
				}
			    if (!$connection_master = ssh2_connect($masterip, 22)) {
			    	header($_SERVER['SERVER_PROTOCOL'] . ' 501 Internal Server Error', true, 500);
   					die();
			    }
				if (!ssh2_auth_pubkey_file($connection_master, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
   					die("<font color=\"red\">SSH key for master not feat!</font>");
				}
			    $result = explode("	", ssh2_return($connection_master,  "mysql -N -e 'show master status;'"));
				$file = $result[0];
				$position = $result[1];
			    $query = $query. "MASTER_LOG_FILE=\"" .$file. "\", MASTER_LOG_POS=" .$position.";";  
			    unset($connection_master);
			    ssh2_exec($connection, "mysql -N -e 'stop slave;'");
			    if (!empty($query)) ssh2_exec($connection, "mysql -N -e '$query' 2>&1");
			    ssh2_exec($connection, "mysql -N -e 'start slave;'");
			    echo "successful.";
			    break;

			case "top":
				if (!$connection = ssh2_connect($_GET["serverip"], 22)) {
					header($_SERVER['SERVER_PROTOCOL'] . ' 501 Internal Server Error', true, 500);
   					die();
				}
				ssh2_auth_pubkey_file($connection, 'root', 'id_rsa.pub', 'id_rsa', '');
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

			case 'getdata':
				$result = $dbconnection->query("SELECT `id`, `servername`, `la`, `rep`, `500`, `elastic`, `locks` FROM `mymon`.`stats`") or die($dbconnection->error());
				$rows=array();
				while($array = $result->fetch_assoc()) {
					$rows[]=$array;
				}
				header("Content-Type: application/json; charset=utf-8 ");
				echo json_encode($rows);
				break;

			case "confirm":
				$login = no_injection($_GET["username"]);
				$result = $dbconnection->query("UPDATE `mymon`.`users` SET approvied = '1' WHERE login = '$login'") or die($dbconnection->error());
				$result = $dbconnection->query("SELECT email FROM `mymon`.`users` WHERE login = '$login'") or die($dbconnection->error());
				$msg = wordwrap("Hi! Your login ($login) just confirmed. Try to login on https://" .$servername, 70);
				$headers =  "From: mymon@netbox.co\r\nReply-To: himaster@mailer.ag\r\n";
				mail($result->fetch_assoc()['email'], "Mymon registration", $msg, $headers);
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
		if (isset($dbconnection)) unset($dbconnection);
    }
	else
		echo 'Неправильное имя или пароль в куках???';
} elseif(isset($_POST['auth_submit'])) {
	$login = no_injection($_POST['login']);
	$password = md5(no_injection($_POST['password']));
	$result = $dbconnection->query("SELECT id, login, password, email FROM `mymon`.`users` WHERE login ='{$login}' AND password='{$password}' AND approvied='1' LIMIT 1") or die($dbconnection->error());
	if ($result->num_rows == 1) {
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
	include "header.html";
	include "auth.html";
	include "footer.html";
}
?>