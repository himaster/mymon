<?php
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
		echo 'Неправильное имя или пароль';
}
else {
?> 
<table>
	<form action="avt.php" method="POST">
		<tr>
			<td>Имя</td>
			<td><input type="text" name="login"></td>
		</tr>
		<tr>
			<td>Пароль</td>
			<td><input type="password" name="password"></td>
		</tr>
		<tr>
			<td colspan="2"> <input type="submit" value="OK" name="auth_submit"></td>
		</tr>
	</form>
</table>
<a href="reggy.html">Регистрация</a>
<?php
}
?>