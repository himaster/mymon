<?php
	if (!$connection = ssh2_connect($_GET["serverip"], 22)) {
		header($_SERVER['SERVER_PROTOCOL'] . ' 501 Internal Server Error', true, 500);
		die();
	}
	ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '');
?>
	<div class="back_menu">
		<a href="#" onclick="self.close()">
			<img src="images/back.png">
		</a>
	</div>
	<div class="textstyle">
<?php
	$str = ssh2_return($connection, "ps aux --sort=-pcpu | head -n 30"); 
	$str = nl2br($str);
	echo($str);
?>
	</div>
