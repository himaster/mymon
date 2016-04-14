<div class="left_button" id="back_button">
	<a href="http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) ?>"><</a>
</div>
<table class="main_table">
	<col span="5">
	<tr class="title">
        <td class="serv">Server</td>
        <td class="la">IP address</td>
        <td class="role">Role</td>
        <td class="rep">Replication</td>
        <td class="loc">Locks</td>
        <td class="500">500s</td>
        <td class="el">Elastic</td>
        <td class="mon">Mongo</td>
        <td class="red">Redis</td>
        <td class="color">Color</td>
    </tr>
<?php
	$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");
	$result = $dbconnection->query("SELECT `id`, `servername`, `ip`, `role`, `db`, `mysql`, `err`, `el`, `mon`, `red`, `color` FROM `mymon`.`stats` ORDER BY `servername`;") or die($dbconnection->error);
	while ($row_user = $result->fetch_assoc()) { ?>
	<tr>
		<td class="serv"><input id="<?php echo trim($row_user['servername']) ?>^servername" type="text" value="<?php echo trim($row_user['servername']) ?>" onchange="javascript: editor(this.id, this.value);"></td>
		<td class="ipaddr"><input id="<?php echo trim($row_user['servername']) ?>^ip" type="text" value="<?php echo trim($row_user['ip']) ?>" onchange="javascript: editor(this.id, this.value); "></td>
		<td class="role"><input id="<?php echo trim($row_user['servername']) ?>^role" type="text" value="<?php echo trim($row_user['role']) ?>" onchange="javascript: editor(this.id, this.value); "></td>
		<td class="rep"><input id="<?php echo trim($row_user['servername']) ?>^db" type="checkbox" <?php if (trim($row_user['db']) == 1) echo "checked "; ?> onchange="javascript: editor(this.id, this.checked);"></td>
		<td class="loc"><input id="<?php echo trim($row_user['servername']) ?>^mysql" type="checkbox" <?php if (trim($row_user['mysql']) == 1) echo "checked "; ?> onchange="javascript: editor(this.id, this.checked);"></td>
		<td class="500"><input id="<?php echo trim($row_user['servername']) ?>^err" type="checkbox" <?php if (trim($row_user['err']) == 1) echo "checked "; ?> onchange="javascript: editor(this.id, this.checked);"></td>
		<td class="el"><input id="<?php echo trim($row_user['servername']) ?>^el" type="checkbox" <?php if (trim($row_user['el']) == 1) echo "checked "; ?> onchange="javascript: editor(this.id, this.checked);"></td>
		<td class="mon"><input id="<?php echo trim($row_user['servername']) ?>^mon" type="checkbox" <?php if (trim($row_user['mon']) == 1) echo "checked "; ?> onchange="javascript: editor(this.id, this.checked);"></td>
		<td class="red"><input id="<?php echo trim($row_user['servername']) ?>^red" type="checkbox" <?php if (trim($row_user['red']) == 1) echo "checked "; ?> onchange="javascript: editor(this.id, this.checked);"></td>
		<td class="color"><input id="<?php echo trim($row_user['servername']) ?>^color" type="color" <?php if (trim($row_user['color']) == 1) echo "checked "; ?> oninput="javascript: editor(this.id, this.value);"></td>
	</tr>
	<?php } ?>
</table>
<div id="test_div" class="status_bar"></div>