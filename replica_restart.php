<?php

if ($_SERVER["SCRIPT_NAME"] != "/index.php") {
    die();
}

$backin = array(
            "88.198.182.130",
            "88.198.182.132",
            "88.198.182.134",
            "88.198.182.146",
            "88.198.182.160",
            "88.198.182.162"
            );
$backout = array("217.118.19.156","pkwteile.no-ip.biz","188.138.234.38");
if (in_array($_GET['serverip'], $backin)) {
    $masterip = "88.198.182.134";
    $query = "CHANGE MASTER TO
              MASTER_HOST=\"10.0.0.3\",
              MASTER_USER=\"replication\",
              MASTER_PASSWORD=\"ZsppM0H9q1hcKTok7O51\", ";
} elseif (in_array($_GET['serverip'], $backout)) {
    $masterip = "88.198.182.134";
    $query = "CHANGE MASTER TO
              MASTER_HOST=\"88.198.182.134\",
              MASTER_USER=\"replication\",
              MASTER_PASSWORD=\"ZsppM0H9q1hcKTok7O51\", ";
} elseif ($_GET['serverip'] == "136.243.42.200") {
    $masterip = "136.243.43.35";
    $query = "CHANGE MASTER TO
              MASTER_HOST=\"10.0.0.2\",
              MASTER_USER=\"replication\",
              MASTER_PASSWORD=\"ZsppM0H9q1hcKTok7O51\", ";
}
if (!$connection = ssh2_connect($_GET["serverip"], 22)) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 501 Internal Server Error', true, 500);
    die("Can't connect to slave server");
}
if (!ssh2_auth_pubkey_file($connection, 'root', $docroot.'/id_rsa.pub', $docroot.'/id_rsa', '')) {
    die("<font color=\"red\">SSH key for {$_GET["serverip"]} not feat!</font>");
}
if (!$conn_master = ssh2_connect($masterip, 22)) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 501 Internal Server Error', true, 500);
    die("Can't connect to master $masterip");
}
if (!ssh2_auth_pubkey_file($conn_master, 'root', $docroot.'/id_rsa.pub', $docroot.'/id_rsa', '')) {
    die("<font color=\"red\">SSH key for master not feat!</font>");
}

$result = explode("	", ssh2_return($conn_master, "mysql -N -e 'show master status;'"));
$file = $result[0];
$position = $result[1];
$query = $query. "MASTER_LOG_FILE=\"" .$file. "\", MASTER_LOG_POS=" .$position.";";
unset($conn_master);
ssh2_exec($connection, "mysql -N -e 'stop slave;'");
if (!empty($query)) {
    ssh2_exec($connection, "mysql -N -e '$query' 2>&1");
}
ssh2_exec($connection, "mysql -N -e 'start slave;'");
echo "successful.";
