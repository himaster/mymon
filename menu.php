<div class="left_button" style="top: 20px;">
	<a href="index.php?task=editor"><img src="images/button.png"></a>
</div>
<div class="left_button" style="top: 50px;">
	<a href="#" onclick="toggle_visibility('my_div')"><img src="images/profile.png"></a>
</div>
<div id="my_div" class="menu">
	
		<b>Profile</b><p>
		<form action="reg.php" method="POST">
			Username:<input class="username" type="text" id="login" name="login" value="<?php echo $login ?>" readonly>
			<p><div id="password"  style="display: none;"> Password:<input type="password" /></div>
			<p><div id="password2"  style="display: none;"> Repeat:<input type="password" /></div>
			<p><div id="email"  style="display: none;"> E-Mail:<input type="text" value="<?php echo mysql_fetch_assoc($sql)['email'] ?>" /></div>
			<p><div id="submit" style="display: none; "><input type="submit" id="submit_edit" name="submit_edit" /></div>
			<p><a href="javascript: toggle_visibility('password'); toggle_visibility('password2'); toggle_visibility('email'); toggle_visibility('submit');">Edit profile</a>
			<p>IP: <?php echo $_SERVER['REMOTE_ADDR'] ?></p>
			<p><a href="http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) ?>/?task=exit">logout</a>
		</form>

</div>