<html>
<head>
	<title>Test</title>
</head>
<body>
	<form name="form" method="post" action="/test.php">
		<select name="uselect">
		<?php
			require_once "functions.php";
			$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");
			$result = $dbconnection->query("SELECT `id`, `login`  FROM `mymon`.`users` WHERE approvied='1'") or die($dbconnection->error);
			while($array = $result->fetch_assoc()) {
				$uid = $array["id"];
				$ulogin = $array["login"];
		  		echo "<option value=\"$uid\">$ulogin</option>";
			}
		?>
		</select>
	</form>
</body>
</html>