<?php
	include "header.html";
?>
<div class="register">
	<table>
		<form action="index.php" method="POST">
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
</div>
<a href="register.php">Регистрация</a>
<?php
	include "footer.html";
?>
