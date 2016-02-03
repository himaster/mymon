<?php
$result = $dbconnection->query("SELECT * FROM `mymon`.`user_roles` WHERE `user_id` = {$uid} AND `role_id` = 1");
if ($result->num_rows == 1) { ?>
<div class="left_button">
	<a href="index.php?task=editor"><img src="images/button.png"></a>
</div>
<div class="left_button" style="top: 50px;">
<?php } else { ?>
<div class="left_button">
<?php } ?>
	<a href="#" onclick="toggle_visibility('my_div')"><img src="images/profile.png"></a>
</div>
<div id="my_div" class="menu">
	
		<div id="menu_title"><b>Profile</b></div>
		<form action="reg.php" method="POST">
			<p>Username:<input class="username" type="text" id="login" name="login" value="<?php echo $login ?>" readonly>
			<p>
			<div id="password_div"> Password:
				<input type="password" id="password" name="password">
			</div>
			<p>
			<div id="password2_div"> Repeat:
				<input type="password" id="password2" name="password2">
			</div>
			<p>
			<div id="email_div"> E-Mail:
				<input type="text" id="email" name="email" value="<?php echo $umail ?>">
			</div>
			<p>
			<div id="simple_div">Test:
				<input type="checkbox" id="la" <?php if ($ula == 1) echo "checked "; ?> >
				<input type="checkbox" id="rep" <?php if ($urep == 1) echo "checked "; ?> >
				<input type="checkbox" id="loc" <?php if ($uloc == 1) echo "checked "; ?> >
				<input type="checkbox" id="500" <?php if ($u500 == 1) echo "checked "; ?> >
				<input type="checkbox" id="el" <?php if ($uel == 1) echo "checked "; ?> >
				<input type="checkbox" id="mon" <?php if ($umon == 1) echo "checked "; ?> >
				<input type="checkbox" id="red" <?php if ($ured == 1) echo "checked "; ?> >
			</div>
			<p>
			<div id="submit_div">
				<input type="submit" id="submit_edit" name="submit_edit" valus="send">
			</div>
			
			<p>
			<a href="javascript: toggle_visibility('password_div');
					toggle_visibility('password2_div');
					toggle_visibility('email_div');
					toggle_visibility('submit_div');
					toggle_visibility('simple_div');">Edit profile</a>
			<p>IP: <?php echo $_SERVER['REMOTE_ADDR'] ?></p>
			<p><a href="https://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) ?>/?task=exit">logout</a>
		</form>

</div>