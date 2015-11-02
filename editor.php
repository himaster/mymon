<?php

$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");
$result = $dbconnection->query("SELECT * FROM `mymon`.`servers`;") or die($connection->error());
$serverinfo = array();
echo "<table class=\"main_table\">";
while ($row_user = $result->fetch_assoc()) {
?>
	<tr>
		<td><input value="<?php echo trim($row_user['id']) ?>"></td>
		<td><input value="<?php echo trim($row_user['ip']) ?>"></td>
		<td><input value="<?php echo trim($row_user['servername']) ?>"></td>
		<td><input value="<?php echo trim($row_user['db']) ?>"></td>
		<td><input value="<?php echo trim($row_user['err']) ?>"></td>
		<td><input value="<?php echo trim($row_user['el']) ?>"></td>
	</tr>
<?php
}
echo ("</table>");