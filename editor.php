<div class="left_button" id="back_button">
	<a href="http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) ?>"><</a>
</div>
<table class="main_table">
	<col span="5">
	<tr class="title">
        <td>Server</td>
        <td>Load Averages</td>
        <td>Role</td>
        <td>Replication</td>
        <td>Locks</td>
        <td>500s</td>
        <td>Elastic</td>
        <td>Mongo</td>
        <td>Redis</td>
    </tr>
<?php
	$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");
	$result = $dbconnection->query("SELECT `id`, `servername`, `ip`, `role`, `db`, `mysql`, `err`, `el`, `mon`, `red` FROM `mymon`.`stats` ORDER BY `servername`;") or die($dbconnection->error);
	while ($row_user = $result->fetch_assoc()) { ?>
	<tr>
		<td><input id="<?php echo trim($row_user['servername']) ?>^servername" type="text" value="<?php echo trim($row_user['servername']) ?>" onchange="javascript: editor(this.id, this.value);"></td>
		<td><input id="<?php echo trim($row_user['servername']) ?>^ip" type="text" value="<?php echo trim($row_user['ip']) ?>" onchange="javascript: editor(this.id, this.value); "></td>
		<td><input id="<?php echo trim($row_user['servername']) ?>^role" type="text" value="<?php echo trim($row_user['role']) ?>" onchange="javascript: editor(this.id, this.value); "></td>
		<td><input id="<?php echo trim($row_user['servername']) ?>^db" type="checkbox" <?php if (trim($row_user['db']) == 1) echo "checked "; ?> onchange="javascript: editor(this.id, this.checked);"></td>
		<td><input id="<?php echo trim($row_user['servername']) ?>^mysql" type="checkbox" <?php if (trim($row_user['mysql']) == 1) echo "checked "; ?> onchange="javascript: editor(this.id, this.checked);"></td>
		<td><input id="<?php echo trim($row_user['servername']) ?>^err" type="checkbox" <?php if (trim($row_user['err']) == 1) echo "checked "; ?> onchange="javascript: editor(this.id, this.checked);"></td>
		<td><input id="<?php echo trim($row_user['servername']) ?>^el" type="checkbox" <?php if (trim($row_user['el']) == 1) echo "checked "; ?> onchange="javascript: editor(this.id, this.checked);"></td>
		<td><input id="<?php echo trim($row_user['servername']) ?>^mon" type="checkbox" <?php if (trim($row_user['mon']) == 1) echo "checked "; ?> onchange="javascript: editor(this.id, this.checked);"></td>
		<td><input id="<?php echo trim($row_user['servername']) ?>^red" type="checkbox" <?php if (trim($row_user['red']) == 1) echo "checked "; ?> onchange="javascript: editor(this.id, this.checked);"></td>
	</tr>
	<?php } ?>
</table>