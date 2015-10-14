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
		echo "<input class=\"usename\" type=\"text\" id=\"login\" name=\"login\" value=\"".$login."\" readonly>";
		echo "<p><input type=\"password\" id=\"password\" name=\"password\" style=\"display: none; \">";
		echo "<p><input type=\"password\" id=\"password2\" name=\"password2\" style=\"display: none; \">";
		echo "<p><input type=\"text\" id=\"email\" name=\"email\" style=\"display: none; \" value=\"".mysql_fetch_assoc($sql)["email"]."\">";
		echo "<p><input type=\"submit\" id=\"submit_edit\" name=\"submit_edit\" style=\"display: none; \">";
		echo "<p><a href=\"javascript: toggle_visibility('password'); toggle_visibility('password2'); toggle_visibility('email'); toggle_visibility('submit_edit');\">Edit profile</a>";
		echo "<p>IP: ".$_SERVER['REMOTE_ADDR']."</p>";
		echo "<p><a href='http://". $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) ."?task=exit'>logout</a>";
		echo "</form>";
	?>
	</span>
</div>