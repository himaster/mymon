<?php
error_reporting(E_ALL);

require_once "functions.php";

$hosts = "mongodb";
$args = "";
$db = "admin";

$datetime1 = new DateTime($val1);

$c  = new MongoClient($hosts); // connect
$mongo = new MongoDB($c, $db);
$mongodb_info = $mongo->command(array('serverStatus'=>true));

$datetime2 = new DateTime($val2);
var_dump($datetime1->diff($datetime2));
echo("Conn: ".$mongodb_info['connections']['current']);
