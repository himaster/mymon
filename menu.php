<div class="left_button" style="top: 20px;">
	<a href="index.php?task=editor"><img src="./images/button.png"></a>
</div>
<div class="left_button" style="top: 50px;">
	<a href="#" onclick="toggle_visibility('my_div')"><img src="./images/profile.png"></a>
</div>
<div id="my_div" class="menu">
	<img src="./images/menu.png">
	<span class="menu_span">
	<b>Profile</b><p>
	<?php 
		echo "<p>".$_COOKIE["mymon"]["login"];
		echo "<p>Change password";
		echo "<p>Change e-mail";
		echo "<p>IP: ".$_SERVER['REMOTE_ADDR']."</p>";
		echo "<p><a href='http://". $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) ."?task=exit'>logout</a>";
	?>
	</span>
</div>