<?php
if (isset($_GET['serverip'])) {
    $serverip = $_GET["serverip"];
} else {
    die("Server IP not set");
}
?>
<html>
<head>
    <title>500 Errors of <?php echo $serverip; ?></title>

    <style>
	.menu {
	    position: fixed;
	}
    </style>
    <link rel="icon" href="http://<?php echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; ?>/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="http://<?php echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; ?>/favicon.ico" type="image/x-icon">
</head>

<body bgcolor="black" text="lightgray" style="margin: 20;">
	<div style="position: fixed; z-index: 9999; width: 30px; height: 200px; overflow: hidden; left: 0px; top: 20px;">
		<a href="#" onclick="self.close()"><img src="./images/back.png"></a>
	</div>
<?php
    $connection1 = ssh2_connect($serverip, 22);
    if (! ssh2_auth_pubkey_file($connection1, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
	die('Public Key Authentication Failed');
    }
    $stream1 = ssh2_exec($connection1, "cat /var/log/500.errs");
    stream_set_blocking($stream1, true);
    $str = stream_get_contents($stream1);
    echo nl2br($str);
    unset($connection1);
?>
    </body>
</html>
