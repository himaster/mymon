<?php

$repository = $_GET['tag'];
$serverip = $_GET['ip'];


if (( ! $connection = ssh2_connect($serverip, 22, $ssh_callbacks))
    or ( ! ssh2_auth_pubkey_file($connection, 'developer', $docroot.'/id_rsa.pub', $docroot.'/id_rsa', ''))) {
    common_log($servername." - retry #".$i++.".");
	echo "SSH Connection error!";
	die();
}

$str = ssh2_return($connection, "cd /home/developer/www/fuel.".$repository."/ && git pull");

echo $str;
