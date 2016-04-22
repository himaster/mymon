<a href="http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) ?>">
	<div class="left_button" id="back_button">
		<img src="images/back.png">
	</div>
</a>
<table class="main_table table-striped">
	<col span="5">
	<tr class="title">
        <td class="loc" width="60px">UID</td>
        <td>Login</td>
        <td>Email</td>
        <td>Role</td>
        <td>Approvied</td>
        <td>Delete</td>

    </tr>
<?php
	$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");
	$result = $dbconnection->query("SELECT `id`, `name`
									FROM `mymon`.`roles`") or die($dbconnection->error);
	while($row = $result->fetch_assoc()){
    	$roles[intval($row['id'])] = $row['name'];
	}
	echo "<pre>";
	var_dump($roles);
	echo "</pre>";
	die();
	$result = $dbconnection->query("SELECT `id`, `login`, `email`, `approvied`, GROUP_CONCAT(`ur`.`role_id`) AS roles
									FROM `mymon`.`users` 
									LEFT JOIN `mymon`.`user_roles` AS `ur` ON (`id` = `ur`.`user_id`)
									GROUP BY `id`;") or die($dbconnection->error);
	while ($row_user = $result->fetch_assoc()) { ?>
		<tr>
			<td><input id="<?php echo trim($row_user['login']) ?>^uid" type="text" value="<?php echo trim($row_user['id']) ?>" onchange="javascript: users_editor(this.id, this.value);"></td>
			<td><input id="<?php echo trim($row_user['login']) ?>^login" type="text" value="<?php echo trim($row_user['login']) ?>" onchange="javascript: users_editor(this.id, this.value); "></td>
			<td><input id="<?php echo trim($row_user['login']) ?>^email" type="text" value="<?php echo trim($row_user['email']) ?>" onchange="javascript: users_editor(this.id, this.value); "></td>
			<td><select id="<?php echo trim($row_user['login']) ?>^role" multiple onchange="javascript: users_editor(this.id, this.value); ">
			<?php

			?>
			</select></td>
			<!-- <td><input id="<?php echo trim($row_user['login']) ?>^role" type="text" value="<?php echo trim($row_user['roles']) ?>" onchange="javascript: users_editor(this.id, this.value);"></td> -->
	
			<td><input id="<?php echo trim($row_user['login']) ?>^approvied" type="checkbox" <?php if (trim($row_user['approvied']) == 1) echo "checked "; ?> onchange="javascript: users_editor(this.id, this.checked);"></td>
		</tr>
	<?php } ?>
</table>
<div id="test_div" class="status_bar"></div>