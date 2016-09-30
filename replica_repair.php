<?php

if ($_SERVER["SCRIPT_NAME"] != "/index.php") {
    die();
}

if (!$connection = ssh2_connect($_GET["serverip"], 22)) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 501 Internal Server Error', true, 500);
    die("Can't connect to slave server");
}
if (!ssh2_auth_pubkey_file($connection, 'root', $docroot.'/id_rsa.pub', $docroot.'/id_rsa', '')) {
    die("<font color=\"red\">SSH key for {$_GET["serverip"]} not feat!</font>");
}
$query = "SET GLOBAL SQL_SLAVE_SKIP_COUNTER=1;";
ssh2_exec($connection, "mysql -N -e 'stop slave;'");
if (!empty($query)) {
    ssh2_exec($connection, "mysql -N -e '$query' 2>&1");
}
ssh2_exec($connection, "mysql -N -e 'start slave;'");
echo "successful.";
