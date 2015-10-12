<div class="left_button" style="top: 20px;">
	<a href="editor.php"><img src="./images/button.png"></a>
</div>
<div class="left_button" style="top: 50px;">
	<a href="#" onclick="toggle_visibility('my_div')"><img src="./images/profile.png"></a>
</div>
<div id="my_div" class="menu">
	<img src="./images/menu.png">
	<span class="menu_span">
	<b>Profile</b><p>
	<?php if (isset($_SERVER['PHP_AUTH_USER'])) {
		echo $_SERVER['PHP_AUTH_USER'];
		echo "<p>IP: ".$_SERVER['REMOTE_ADDR']."</p>";
		echo "<p><a href='http://logout@".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."'>logout</a>";
	} else {
		echo "Local user";
		echo "<p>IP: ".$_SERVER['REMOTE_ADDR']."</p>";
	}
	?>
	</span>
</div>