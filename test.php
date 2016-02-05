<html>
<head>
	<title>Test</title>
</head>
<body>
	<?php
		require_once "functions.php";
		if (isset($_POST['submit'])) {
			print_r($_POST);
		}
		$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");
		$result = $dbconnection->query("SELECT `id`, `login`  FROM `mymon`.`users` WHERE approvied='1'") or die($dbconnection->error);
	?>	
	<form name="form" method="post" action="/test.php">
		<textarea name="umessage"></textarea>
		<p><select multiple name="uselect">
		<?php
			while($array = $result->fetch_assoc()) {
				$uid = $array["id"];
				$ulogin = $array["login"];
		  		echo "<option value=\"$uid\">$ulogin</option>";
			}
		?>
		</select>
		<p><input type="submit" name="submit" value="send">
	</form>
</body>
</html>