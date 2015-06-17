<?php
    if (!isset($_GET['serverip'])){
	   die('Server is not defined!');
    }
    $serverip = $_GET['serverip'];
    $masterip = "88.198.182.130";
    $connection = ssh2_connect($masterip, 22);
    if (! ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
	   die('Public Key Authentication Failed');
    }
    $stream = ssh2_exec($connection, "mysql -N -e 'show master status;' | awk '{print $1}'");
    stream_set_blocking($stream, true);
    $file = stream_get_contents($stream);
    $stream = ssh2_exec($connection, "mysql -N -e 'show master status;' | awk '{print $2}'");
    stream_set_blocking($stream, true);
    $position = stream_get_contents($stream);
    unset($connection);
    $backin = array("88.198.182.132","88.198.182.134","88.198.182.146");
    if (in_array($serverip, $backin)){
	   $query = "CHANGE MASTER TO MASTER_HOST=\"10.0.0.1\", MASTER_USER=\"replication\", MASTER_PASSWORD=\"ZsppM0H9q1hcKTok7O51\", MASTER_LOG_FILE=\"" .$file. "\", MASTER_LOG_POS=" .$position. ";";
    } else {
	   $query = "CHANGE MASTER TO MASTER_HOST=\"88.198.182.130\", MASTER_USER=\"replication\", MASTER_PASSWORD=\"ZsppM0H9q1hcKTok7O51\", MASTER_LOG_FILE=\"" . $file . "\", MASTER_LOG_POS=" . $position . ";";
    }
    $connection = ssh2_connect($serverip, 22);
    if (! ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '')) {
	   die('Public Key Authentication Failed');
    }
    ssh2_exec($connection, "mysql -N -e 'stop slave;'");
    $stream = ssh2_exec($connection, "mysql -N -e '$query'");
    stream_set_blocking($stream, true);
    $result = stream_get_contents($stream);
    echo $result;
    ssh2_exec($connection, "mysql -N -e 'start slave;'");
    unset($connection);
?>
