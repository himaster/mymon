<?php
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
		$login = $_POST['login'];
		$password = $_POST['password'];
		$password2 = $_POST['password2'];
		$email = $_POST['email'];
		$query = "SELECT `id` FROM `users` WHERE `login`='{$login}' AND `password`='{$password}'";
		$sql = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($sql) > 0) {
			echo 'Такой логин уже существует';
		}
 		else {
			$query = "INSERT INTO users(login , password , email )
			VALUES ('$login', '$password', '$email')";
			$result = mysql_query($query) or die(mysql_error());;
			echo 'Регистрация успешно прошла';
		}
	}
}
?> 