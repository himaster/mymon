<?php
if (isset($_GET["exit"])) {
	setcookie('mymon[login]', '', time() - 3600, '/');
	setcookie('mymon[password]', '', time() - 3600, '/');
	die("Logged out.");
	header('Location: http://netbox.co/mymon/');
}

include_once("connect.php");

if (isset($_COOKIE["mymon"])) {
 	$login = $_COOKIE["mymon"]["login"];
	$password = $_COOKIE["mymon"]["password"];
	$query = "SELECT id, login, password FROM users WHERE login ='{$login}' AND password='{$password}' AND approvied='1' LIMIT 1";
	$sql = mysql_query($query) or die(mysql_error());
	if (mysql_num_rows($sql) == 1) {
		include "header.html";
    	include "table.php";
    	include "footer.html";
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