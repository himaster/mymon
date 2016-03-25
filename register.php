<?php
	include "header.html";
?>
<div class="register">
	<table>
		<form action="reg.php" method="POST">
			<tr>
				<td>Имя</td>
				<td><input type="text" name="login" ></td>
			</tr>
			<tr>
				<td>Пароль</td>
				<td><input type="password" name="password" ></td>
			</tr>
			<tr>
				<td>Еще разок</td>
				<td><input type="password" name="password2"></td>
			</tr>
			<tr>
				<td>Email</td>
				<td><input type="text" name="email"></td>
			</tr>
			<tr>
				<p><td colspan="2"><input type="submit" value="OK" name="submit" ></td>
			</tr>
			<tr>
				<td><a href="/"><input type="text" value="Login"></a></td>
			</tr>
		</form>
	</table>
</div>
<?php
	include "footer.html";
?>