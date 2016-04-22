
<a href="#" onclick="toggle_visibility_menu('my_div');
					 if ($('#message_div').is(':visible')) {
					 	toggle_visibility_menu('message_div');
					 } 
					 on_top('my_div');">
	<div id="left_button" class="left_button">
		<img src="images/profile.png">
	</div>
</a>
<a href="javascript: toggle_visibility_msg('message_div');
					 if ($('#my_div').is(':visible')) {
					 	toggle_visibility_menu('my_div');
					 }
					 on_top('message_div');">
	<div id="left_button2" class="left_button">
		<div id="text">
			msg
		</div>
	</div>
</a>
<?php
	if ($isAdmin) { ?>
		<a href="index.php?task=editor">
			<div id="left_button3" class="left_button">
				<img src="images/settings.png">
			</div>
		</a>
		<a href="index.php?task=users_editor">
			<div id="left_button4" class="left_button">
				<img src="images/users.png">
			</div>
		</a>
		<?php 
	} ?>
<div id="my_div" class="menu">
	<b>Profile</b>
	<p><p>
	<form action="reg.php" method="POST">
		IP: <?php echo $_SERVER['REMOTE_ADDR'] ?>
		<p>username:<input class="username" id="login" name="login" value="<?php echo $login ?>">
		<p><a id="profile_edit" href="javascript: toggle_visibility('password_div');
							 toggle_visibility('email_div');
							 toggle_visibility('submit_div');
							 toggle_visibility('simple_div');
							 toggle_visibility('col1');
							 toggle_visibility('col2');
							 toggle_visibility('col3');
							 toggle_visibility('col4');
							 toggle_visibility('col5');
							 toggle_visibility('col6');
							 toggle_visibility('col7');
							 "><input type="button" value="edit profile"></a>
		<p>
		<div id="password_div"> password:
			<input type="password" id="password" name="password">
			<input type="password" id="password2" name="password2" style="display: none;">
		</div>
		<p>
		<p>
		<div id="email_div"> e-mail:
			<input type="text" id="email" name="email" value="<?php echo $umail ?>">
		</div>
		<p>
		<div class="simple_div" id="simple_div" name="simple_div"> columns:
			<p>
			<div style="display: inline-block;">
				1<input type="checkbox" id="la" name="la" <?php if ($ula == 1) echo "checked "; ?> >
			</div>
			<div style="display: inline-block;">
				2<input type="checkbox" id="rep" name="rep" <?php if ($urep == 1) echo "checked "; ?> >
			</div>
			<div style="display: inline-block;">
				3<input type="checkbox" id="loc" name="loc" <?php if ($uloc == 1) echo "checked "; ?> >
			</div>
			<div style="display: inline-block;">
				4<input type="checkbox" id="500" name="500" <?php if ($u500 == 1) echo "checked "; ?> >
			</div><p>
			<div style="display: inline-block;">
				5<input type="checkbox" id="el" name="el" <?php if ($uel == 1) echo "checked "; ?> >
			</div>
			<div style="display: inline-block;">
				6<input type="checkbox" id="mon" name="mon" <?php if ($umon == 1) echo "checked "; ?> >
			</div>
			<div style="display: inline-block;">
				7<input type="checkbox" id="red" name="red" <?php if ($ured == 1) echo "checked "; ?> >
			</div>
			<p>
			<div style="display: inline-block;">
				<p>Notifications: 
				<p><input type="checkbox" id="notify" name="notify" <?php if ($unotify == 1) echo "checked "; ?> >
			</div>
		</div>
		<p>
		<div id="submit_div">
			<input type="submit" id="submit_edit" name="submit_edit" value="save">
		</div>		
		<p>
		<p><a href="<?php echo host_scheme() ?>://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) ?>/?task=exit"><input type="button" value="logout"></a>
	</form>
</div>
<div id="message_div" class="menu">
	<b>Messaging</b>
	<p><p>
	<?php
		$result = $dbconnection->query("SELECT `id`, `login`  FROM `mymon`.`users` WHERE approvied='1'") or die($dbconnection->error);
	?>	
	<form method="post" name="message_form" id="message_form" action="javascript:msg_submit();">
		<textarea name="umessage" id="umessage" class="umessage"></textarea>
		<p><select multiple name="uselect[]">
		<?php
			while($array = $result->fetch_assoc()) {
				echo "<option value=\"".$array["id"]."\">".$array["login"]."</option>";
			}
		?>
		</select>
		<p><input type="submit" name="message_submit" id="message_submit" value="send">
	</form>
</div>