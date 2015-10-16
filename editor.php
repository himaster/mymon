
<div class="left_button" style="top: 20px;">
	<a href="http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) ?>"><img src="images/back.png"></a>
</div>
<div align="center">
	<h2>Server list</h2>
	<h4>IP&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspname&nbspweb&nbspDB</h4>
	<form action="index.php?task=editor" method="post">
		<textarea name="text" cols="30" rows="<?php count($mass) ?>" class="editor"><?php echo htmlspecialchars($text) ?></textarea>
		<p><input type="submit" value="Сохранить" onClick="window.location.href='http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) ?>'" />
		<input type="reset" />
	</form>
</div>	