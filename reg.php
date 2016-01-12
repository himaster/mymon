<?php
include_once("functions.php");

error_reporting(E_ALL);

$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");

if (isset($_POST['submit'])) {
	if(empty($_POST['login'])) echo 'Вы не ввели логин';
	elseif(empty($_POST['password'])) echo 'Вы не ввели пароль';
	elseif(empty($_POST['password2'])) echo 'Вы не ввели подтверждение пароля';
	elseif($_POST['password'] != $_POST['password2']) echo 'Введенные пароли не совпадают';
	elseif(empty($_POST['email'])) echo 'Вы не ввели E-mail';
	else {
		$login = no_injection($_POST['login']);
		$password = md5(no_injection($_POST['password']));
		$email = no_injection($_POST['email']);
		$result = $dbconnection->query("SELECT `id` FROM `users` WHERE `login`='{$login}' AND `password`='{$password}'") or die($dbconnection->error());
		if ($result->num_rows > 0) echo 'Такой логин уже существует';
 		else {
			$result = $dbconnection->query("INSERT INTO users(login , password , email, approvied) VALUES ('$login', '$password', '$email', '0')") or die($dbconnection->error());
			$msg = "<html><head><title></title></head><body>";
			$msg .= "User $login ($email) just registered. Click <form action='https://mymon.pkwteile.de/index.php' method='get'>";
			$msg .= "<input type='hidden' name='task' value='confirm' \>";
			$msg .= "<input type='hidden' name='username' value=$login \>";
			$msg .= "<input type='submit' value='here' \>";
			$msg .= "</form> to confirm.";
			$msg .= "</body></html>";
			$to = "himaster@mailer.ag";
			$subject = "Mymon registration";
			$headers = "From: mymon@pkwteile.de\r\n";
			$headers .= "Reply-To: himaster@mailer.ag\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			mail($to, $subject, $msg, $headers);
			echo "Регистрация успешно прошла. Ожидайте письма с подтверждением.";
		}
	}
}
elseif (isset($_POST['submit_edit'])) {
	if(empty($_POST['password'])) echo 'Вы не ввели пароль';
	elseif(empty($_POST['password2'])) echo 'Вы не ввели подтверждение пароля';
	elseif($_POST['password'] != $_POST['password2']) echo 'Введенные пароли не совпадают';
	elseif(empty($_POST['email'])) echo 'Вы не ввели E-mail';
	else {
		$login = no_injection($_POST['login']);
		$password = md5(no_injection($_POST['password']));
		$email = no_injection($_POST['email']);
		$query = "UPDATE users SET password = '$password', email = '$email' WHERE login = '$login'";
		$result = mysql_query($query) or die(mysql_error());
		echo "Профиль успешно обновлен.<a href=\"https://" .$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']). "/?task=exit\">Войти</a>";
	}
}
else {
	echo "None selected";
}
?> 