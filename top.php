<html>
<head>
    <title>Process list</title>
    <meta id="autoRefresh" http-equiv="refresh" content="5" />
    <link rel="icon" href="http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']); ?>/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']); ?>/favicon.ico" type="image/x-icon">
</head>

<body bgcolor="black" text="lightgray" style="margin: 20;">
<div style="position: fixed; z-index: 9999; width: 30px; height: 200px; overflow: hidden; left: 0px; top: 20px;">
	<a href="#" onclick="self.close()"><img src="./images/back.png"></a>
</div>
<?php
if (isset($_GET['serverip'])){
	$serverip = $_GET['serverip'];
	$connection = ssh2_connect($serverip, 22);
	if (! ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
		die('Public Key Authentication Failed');
	}
	$stream = ssh2_exec($connection, "ps aux --sort=-pcpu | head -n 30");
	stream_set_blocking($stream, true);
	$str = stream_get_contents($stream);
	$str = nl2br($str);
	echo($str);
	unset($connection);
} else {
	echo "Not enough params.";
}
?>
</body>
</html>