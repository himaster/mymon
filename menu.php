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
	<?php 
		echo $_COOKIE["mymon"]["login"];
		echo "<p>IP: ".$_SERVER['REMOTE_ADDR']."</p>";
		echo "<p><a href='http://". $_SERVER['SERVER_NAME'] ."/?exit'>logout</a>";
	?>
	</span>
</div>