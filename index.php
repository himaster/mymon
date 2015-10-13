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
					include "header.html";
					echo("<div class=\"err500_menu\">");
					echo("<a href=\"#\" onclick=\"self.close()\">");
					echo("<img src=\"./images/back.png\"></a>");
					echo("</div><div class=\"err500\">");

					$connection1 = ssh2_connect($_GET["serverip"], 22);
					if (! ssh2_auth_pubkey_file($connection1, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
   						die('Public Key Authentication Failed');
					}
					$stream1 = ssh2_exec($connection1, "cat /var/log/500.errs");
					stream_set_blocking($stream1, true);
					$str = stream_get_contents($stream1);
					echo nl2br($str);
					echo "</div>";
					include "footer.html";
					unset($connection1);
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