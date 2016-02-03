<?php
error_reporting(E_ALL);

require_once "functions.php";

$hosts = "mongodb";
$args = "";
$db = "admin";

$c  = new MongoClient($hosts); // connect


$mongo = new MongoDB($c, $db);

$mongodb_info = $mongo->command(array('serverStatus'=>true));
$mongodb_connections = $mongodb_info['connections']['current'];

die($mongodb_connections);