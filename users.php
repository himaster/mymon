<a href="http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) ?>">
	<div class="left_button" id="back_button">
		<
	</div>
</a>
<table class="main_table">
	<col span="5">
	<tr class="title">
        <td>UID</td>
        <td>Login</td>
        <td>Email</td>
        <td>Role</td>
        <td>Approvied</td>
        <td>Delete</td>

    </tr>
<?php
	$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");
	$result = $dbconnection->query("SELECT `id`, `login`, `email`, `approvied`, GROUP_CONCAT(`ur`.`role_id`) AS roles
									FROM `mymon`.`users` 
									LEFT JOIN `mymon`.`user_roles` AS `ur` ON (`id` = `ur`.`user_id`)
									GROUP BY `id`;") or die($dbconnection->error);
	while ($row_user = $result->fetch_assoc()) { ?>
		<tr>
			<td><input id="<?php echo trim($row_user['login']) ?>^uid" type="text" value="<?php echo trim($row_user['id']) ?>" onchange="javascript: editor(this.id, this.value);"></td>
			<td><input id="<?php echo trim($row_user['login']) ?>^login" type="text" value="<?php echo trim($row_user['login']) ?>" onchange="javascript: editor(this.id, this.value); "></td>
			<td><input id="<?php echo trim($row_user['login']) ?>^email" type="text" value="<?php echo trim($row_user['email']) ?>" onchange="javascript: editor(this.id, this.value); "></td>
			<td><input id="<?php echo trim($row_user['login']) ?>^role" type="text" value="<?php echo trim($row_user['roles']) ?>" onchange="javascript: editor(this.id, this.checked);"></td>
			<td><input id="<?php echo trim($row_user['login']) ?>^approvied" type="checkbox" <?php if (trim($row_user['approvied']) == 1) echo "checked "; ?> onchange="javascript: editor(this.id, this.checked);"></td>
		</tr>
	<?php } ?>
</table>
<div id="test_div" class="status_bar"></div>