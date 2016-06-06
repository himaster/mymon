<?php

if (! $ip_addr = $_GET['ip_addr']) {
    die("No IP address to ban.");
}
$result = $dbconnection->query("INSERT IGNORE INTO `firewall`.`blacklist`
                                SET `ip` = '$ip_addr';") or
die($dbconnection->error());
if (!$connection = ssh2_connect("balancer1", 22)) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 501 Internal Server Error', true, 500);
    die("Can't connect to server");
}
if (!ssh2_auth_pubkey_file($connection, 'root', $docroot.'/id_rsa.pub', $docroot.'/id_rsa', '')) {
    die("<font color=\"red\">SSH key for server not feat!</font>");
}
ssh2_return($connection, "/etc/firewall/firewall_new.sh") or die("Firewall error!");
echo "IP address banned.";
