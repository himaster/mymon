<div class="left_button" style="top: 20px;">
	<a href="index.php?task=editor"><img src="images/button.png"></a>
</div>
<div class="left_button" style="top: 50px;">
	<a href="#" onclick="toggle_visibility('my_div')"><img src="images/profile.png"></a>
</div>
<div id="my_div" class="menu">
	
		<div style="text-align: center; "><b>Profile</b></div>
		<form action="reg.php" method="POST">
			<p>Username:<input class="username" type="text" id="login" name="login" value="<?php echo $login ?>" readonly>
			<p><div id="password_div"> Password:<input type="password" id="password" name="password"></div>
			<p><div id="password2_div"> Repeat:<input type="password" id="password2" name="password2"></div>
			<p><div id="email_div"> E-Mail:<input type="text" id="email" name="email" value="<?php echo mysql_fetch_assoc($sql)['email'] ?>"></div>
			<p><div id="submit_div"><input type="submit" id="submit_edit" name="submit_edit"></div>
			<p><a href="javascript: toggle_visibility('password_div'); toggle_visibility('password2_div'); toggle_visibility('email_div'); toggle_visibility('submit_div');">Edit profile</a>
			<p>IP: <?php echo $_SERVER['REMOTE_ADDR'] ?></p>
			<p><a href="http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) ?>/?task=exit">logout</a>
		</form>

</div>