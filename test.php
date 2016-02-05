<html>
<head>
	<title>Test</title>
</head>
<body>
	<?php
		require_once "functions.php";
		$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");
		if (isset($_POST['submit'])) {
			$umessage = no_injection($_POST['umessage']);
			$ulogins = array_merge(array(0 => ' '), $_POST['uselect']);
			$str = implode(" ,", $ulogins);
			$query = "INSERT INTO `mymon`.`messages` (`message`, `sender`, `receiver`) VALUES ('$umessage', '$uid', '$str');";
			$result = $dbconnection->query($query) or die($dbconnection->error);
			echo "Message sent.";
			exit;
		}
		$result = $dbconnection->query("SELECT `id`, `login`  FROM `mymon`.`users` WHERE approvied='1'") or die($dbconnection->error);
	?>	
	<form method="post">
		<textarea name="umessage"></textarea>
		<p><select multiple name="uselect[]">
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