
<table class="main_table"><col span="5">
	<tr class="title">
        <td>Server</td>
        <td>IP Address</td>
        <td>Replication</td>
        <td>500s</td>
        <td>Elastic</td>
        <td>Locks</td>
    </tr>
<?php

$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");
$result = $dbconnection->query("SELECT `id`, `servername`, `ip`, `db`, `mysql`, `err`, `el` FROM `mymon`.`stats`;") or die($dbconnection->error);
$serverinfo = array();

while ($row_user = $result->fetch_assoc()) {
?>
	<tr>
		<td><?php echo trim($row_user['servername']) ?></td>
		<td><?php echo trim($row_user['ip']) ?></td>
		<td><?php echo trim($row_user['db']) ?></td>
		<td><?php echo trim($row_user['mysql']) ?></td>
		<td><?php echo trim($row_user['err']) ?></td>
		<td><?php echo trim($row_user['el']) ?></td>
	</tr>
<?php
}
echo ("</table>");