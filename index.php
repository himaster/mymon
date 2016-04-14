<?php
error_reporting(E_ALL);
include_once("functions.php");

if ($_SERVER['HTTP_HOST'] == "mymon.pkwteile.de") $env="master";
elseif ($_SERVER['HTTP_HOST'] == "mymon.loc") $env="dev";
else header("Location: https://mymon.pkwteile.de/");

if (!isset($_GET['task'])) $_GET['task'] = "NULL";

if ($_GET['task'] == "exit") {
	setcookie('mymon[login]', '', time()-604800, dirname($_SERVER['PHP_SELF']), $_SERVER['HTTP_HOST'], isset($_SERVER["HTTP_X_FORWARDED_PROTOCOL"]), true);
	setcookie('mymon[password]', '', time()-604800, dirname($_SERVER['PHP_SELF']), $_SERVER['HTTP_HOST'], isset($_SERVER["HTTP_X_FORWARDED_PROTOCOL"]), true);
	unset($_COOKIE['mymon']);
	header("Location: https://" .$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
	die();
}

if ($env == "master") $host="127.0.0.1";
else $host="188.138.234.38";
$dbconnection = @new mysqli($host, "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");

if (isset($_COOKIE["mymon"])) {
 	$login = no_injection($_COOKIE["mymon"]["login"]);
	$password = no_injection($_COOKIE["mymon"]["password"]);
	$result = $dbconnection->query("SELECT id, login, password, email, la, rep, loc, `500`, el, mon, red, notify  FROM `mymon`.`users` WHERE login ='" .$login. "' AND password='" .$password. "' AND approvied='1' LIMIT 1") or die($dbconnection->error);
	$result_assoc = $result->fetch_assoc();
	$uid = $result_assoc['id'];
	$umail = $result_assoc['email'];
	$ula = $result_assoc['la'];
	$urep = $result_assoc['rep'];
	$uloc = $result_assoc['loc'];
	$u500 = $result_assoc['500'];
	$uel = $result_assoc['el'];
	$umon = $result_assoc['mon'];
	$ured = $result_assoc['red'];
	$unotify = $result_assoc['notify'];
	if ($result->num_rows == 1) {
		switch ($_GET["task"]) {
			case "500err":
				include "header.html";
				if (!$connection = ssh2_connect($_GET["serverip"], 22)) {
					header($_SERVER['SERVER_PROTOCOL'] . ' 501 Internal Server Error', true, 500);
   					die("Connection error!");
				}
				ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '');
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

			case "users_editor":
				include "header.html";
				include "users.php";		
				include "footer.html";				
				break;

			case "editor_save":
				$servername = $_GET['servername'];
				$columnname = $_GET['columnname'];
				$val = $_GET['val'];
				$query = "UPDATE `mymon`.`stats` SET `$columnname` = '$val' WHERE `servername` = '$servername'";
				$result = $dbconnection->query($query) or die($dbconnection->error());
				echo "Successfully edited";
				break;

			case "users_editor_save":
				$username = $_GET['username'];
				$columnname = $_GET['columnname'];
				$val = $_GET['val'];
				if ($columnname == "role") {
					$user_id = $dbconnection->query("SELECT `id` FROM `mymon`.`users` WHERE `login` = '$username'");
					$query = "DELETE FROM `mymon`.`user_roles` WHERE `user_id` = '$user_id;' ";
					$roles_array = explode(',',$val);
					foreach ($roles_array as $item) {
					    $query .= "INSERT INTO `mymon`.`user_roles`(`user_id`, `role_id`) VALUES ('$user_id', '$item'); ";
					}
				}
				else {
					$query = "UPDATE `mymon`.`users` SET `$columnname` = '$val' WHERE `login` = '$username'";
				}
				//die($query);
				if ($result = $dbconnection->query($query)) echo "Successfully edited";
				else echo $dbconnection->error;
				break;

			case "replica_repair":
			    if (!$connection = ssh2_connect($_GET["serverip"], 22)) {
			    	header($_SERVER['SERVER_PROTOCOL'] . ' 501 Internal Server Error', true, 500);
   					die("Can't connect to slave server");
			    }
				if (!ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
					die("<font color=\"red\">SSH key for {$_GET["serverip"]} not feat!</font>");
				}
			    $query = "SET GLOBAL SQL_SLAVE_SKIP_COUNTER=1;";
			    ssh2_exec($connection, "mysql -N -e 'stop slave;'");
			    if (!empty($query)) ssh2_exec($connection, "mysql -N -e '$query' 2>&1");
			    ssh2_exec($connection, "mysql -N -e 'start slave;'");
			    echo "successful.";
			    break;
			
			case "replica":			    
			    $backin = array("88.198.182.130","88.198.182.132","88.198.182.134","88.198.182.146","88.198.182.160","88.198.182.162");
			    $backout = array("217.118.19.156","pkwteile.no-ip.biz","188.138.234.38");
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
   					die("Can't connect to slave server");
			    }
				if (!ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
					die("<font color=\"red\">SSH key for {$_GET["serverip"]} not feat!</font>");
				}
			    if (!$connection_master = ssh2_connect($masterip, 22)) {
			    	header($_SERVER['SERVER_PROTOCOL'] . ' 501 Internal Server Error', true, 500);
   					die("Can't connect to master $masterip");
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
				ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '');
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
				$rows=array();
				$result = $dbconnection->query("SELECT `id`, UNIX_TIMESTAMP(`timestamp`) as `timestamp`, `servername`, `la`, `rep`, `500`, `elastic`, `locks`, `mongo`, `redis` FROM `mymon`.`stats`") or die($dbconnection->error());
				while($array = $result->fetch_assoc()) {
					$rows["data"][] = $array;
				}
				$result = $dbconnection->query("SELECT `messages`.`id`, UNIX_TIMESTAMP(`messages`.`timestamp`) as `timestamp`, `messages`.`message`, `users`.`login` FROM `mymon`.`messages` JOIN `users` WHERE `messages`.`sender` = `users`.`id` AND `receiver` = '$uid' AND isRead = 0 AND isDeleted = 0 LIMIT 1;") or die($dbconnection->error());
				if(mysqli_num_rows($result)>0){
					$rows["msg"] = $result->fetch_assoc();
				}
				header("Content-Type: application/json; charset=utf-8 ");
				echo json_encode($rows);
				break;

			case "confirm":
				$login = no_injection($_GET["username"]);
				$result = $dbconnection->query("SELECT id FROM `mymon`.`users` WHERE login ='{$login}' LIMIT 1;") or die($dbconnection->error());
				$uid = $result->fetch_assoc()['id'];
				$result = $dbconnection->query("SELECT `id`, `name` FROM `roles`") or die($dbconnection->error());
				while ($row = $result->fetch_assoc()) {
					if ($_GET[$row['name']] == "on") {
						$rid = $row['id'];
						$dbconnection->query("INSERT INTO `user_roles`(`user_id`, `role_id`) VALUES ('$uid', '$rid');") or die($dbconnection->error());
					}
				}
				$result = $dbconnection->query("UPDATE `mymon`.`users` SET approvied = '1' WHERE login = '$login';") or die($dbconnection->error());
				$result = $dbconnection->query("SELECT email FROM `mymon`.`users` WHERE login = '$login';") or die($dbconnection->error());
				$msg = wordwrap("Hi! Your login ($login) just confirmed. Try to login on https://" .$hostname, 70);
				$headers =  "From: mymon@netbox.co\r\nReply-To: himaster@mailer.ag\r\n";
				mail($result->fetch_assoc()['email'], "Mymon registration", $msg, $headers);
				echo "<p>Профиль успешно обновлен";
				break;

			case "sendmsg":
				$umessage = no_injection($_POST['umessage']);
				foreach ($_POST['uselect'] as $ulogin) {
					$result = $dbconnection->query("INSERT INTO `mymon`.`messages` (`message`, `sender`, `receiver`) VALUES ('$umessage', '$uid', '$ulogin');") or die("Error occured".$dbconnection->error);
				}
				echo "Message sent.";
				break;

			case "msgred":
				$result = $dbconnection->query("UPDATE `mymon`.`messages` SET isRead = 1 WHERE receiver ='$uid' and isRead = 0 and isDeleted = 0 LIMIT 1;") or die($dbconnection->error());
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
	$result = $dbconnection->query("SELECT id, login, password, email, la, rep, loc, `500`, el, mon, red, notify  FROM `mymon`.`users` WHERE login ='" .$login. "' AND password='" .$password. "' AND approvied='1' LIMIT 1") or die($dbconnection->error);
	$result_assoc = $result->fetch_assoc();
	$uid = $result_assoc['id'];
	$umail = $result_assoc['email'];
	$ula = $result_assoc['la'];
	$urep = $result_assoc['rep'];
	$uloc = $result_assoc['loc'];
	$u500 = $result_assoc['500'];
	$uel = $result_assoc['el'];
	$umon = $result_assoc['mon'];
	$ured = $result_assoc['red'];
	$unotify = $result_assoc['notify'];
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