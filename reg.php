<?php
include_once("functions.php");
include_once("connect.php");
if (isset($_POST['submit'])) {
	if(empty($_POST['login'])) {
		echo 'Вы не ввели логин';
	}
	elseif(empty($_POST['password'])) {
		echo 'Вы не ввели пароль';
	}
	elseif(empty($_POST['password2'])) {
		echo 'Вы не ввели подтверждение пароля';
	}
	elseif($_POST['password'] != $_POST['password2']) {
		echo 'Введенные пароли не совпадают';
	}
	elseif(empty($_POST['email'])) {
		echo 'Вы не ввели E-mail';
	}
	else {
		$login = no_injection($_POST['login']);
		$password = md5(no_injection($_POST['password']));
		$email = no_injection($_POST['email']);
		$query = "SELECT `id` FROM `users` WHERE `login`='{$login}' AND `password`='{$password}'";
		$sql = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($sql) > 0) {
			echo 'Такой логин уже существует';
		}
 		else {
			$query = "INSERT INTO users(login , password , email, approvied) VALUES ('$login', '$password', '$email', '0')";
			$result = mysql_query($query) or die(mysql_error());
			$msg = "User $login ($email) just registered. Click ".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/index.php?task=confirm&username=$login to confirm.";
			$msg = wordwrap($msg,70);
			$headers =  "From: mymon@netbox.co\r\nReply-To: himaster@mailer.ag\r\n";
			mail("himaster@mailer.ag","Mymon registration",$msg,$headers);
			echo "Регистрация успешно прошла.<a href=\"http://" .$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']). "/?task=exit\">Войти</a>";
		}
	}
}
elseif (isset($_POST['submit_edit'])) {
	if(empty($_POST['password'])) {
		echo 'Вы не ввели пароль';
	}
	elseif(empty($_POST['password2'])) {
		echo 'Вы не ввели подтверждение пароля';
	}
	elseif($_POST['password'] != $_POST['password2']) {
		echo 'Введенные пароли не совпадают';
	}
	elseif(empty($_POST['email'])) {
		echo 'Вы не ввели E-mail';
	}
	else {
		$login = no_injection($_POST['login']);
		$password = md5(no_injection($_POST['password']));
		$email = no_injection($_POST['email']);
		$query = "UPDATE users SET password = '$password', email = '$email' WHERE login = '$login'";
		$result = mysql_query($query) or die(mysql_error());
		echo "Профиль успешно обновлен.<a href=\"http://" .$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']). "/?task=exit\">Войти</a>";
	}
}
else {
	echo "None selected";
}
?> 