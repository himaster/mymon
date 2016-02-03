<?php

#require_once "functions.php";

$hosts = "mongodb";
$db = "admin";

$curTime = microtime(true);
$c  = new MongoClient($hosts);
$mongo = new MongoDB($c, $db);
$mongodb_info = $mongo->command(array('serverStatus'=>true));
$timeConsumed = round(microtime(true) - $curTime,3)*1000; 
echo($timeConsumed);
