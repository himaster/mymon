<?php

$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");
$result = $dbconnection->query("SELECT * FROM `mymon`.`servers`;") or die($connection->error());
$serverinfo = array();
echo "<div class=\"container\"><table class=\"table table-striped editor\">";
while ($row_user = $result->fetch_assoc()) {
?>
	<tr>
		<td width="20px"><input width="100%" value="<?php echo $row_user['id'] ?>"></td>
		<td><input value="<?php echo $row_user['ip'] ?>"></td>
		<td><input value="<?php echo $row_user['servername'] ?>"></td>
		<td><input value="<?php echo $row_user['db'] ?>"></td>
		<td><input value="<?php echo $row_user['err'] ?>"></td>
		<td><input value="<?php echo $row_user['el'] ?>"></td>
	</tr>
<?php
}
echo ("</table></div>");