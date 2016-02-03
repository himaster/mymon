<?php
error_reporting(E_ALL);

require_once "functions.php";

$hosts = "mongodb";
$args = "";
$db = "admin";

$c  = new MongoClient($hosts, $args); // connect


die("test");

$mongo = new MongoDB($c, $db);

$mongodb_info = $mongo->command(array('serverStatus'=>true));
$mongodb_version = $mongodb_info['version'];

die($mongodb_version);