<?php

require_once "functions.php";

$hosts = "mongodb://mongodb";
$args = "";
$db = "admin";

try {
	$c  = new MongoClient($hosts, $args); // connect
}

catch ( MongoConnectionException $e ) {
    echo '<p>Couldn\'t connect to mongodb, is the "mongo" process running?</p>';
    exit();
}
die("test");

$mongo = new MongoDB($c, $db);

$mongodb_info = $mongo->command(array('serverStatus'=>true));
$mongodb_version = $mongodb_info['version'];

die($mongodb_version);