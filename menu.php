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
		echo "<form action=\"reg.php\" method=\"POST\">";
		echo "<input name=\"login\" value=\"".$_COOKIE["mymon"]["login"]."\" editable=\"no\" >";
		echo "<p>Edit passowrd";
		echo "<p>IP: ".$_SERVER['REMOTE_ADDR']."</p>";
		echo "<p><a href='http://". $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) ."?task=exit'>logout</a>";
		echo "</form>";
	?>
	</span>
</div>