#!/bin/env php

<?php

include_once "functions.php";
$dblocation = "localhost";
$dbname = "mymon";
$dbuser = "mymon";
$dbpasswd = "eiGo7iek";
$connection = mysqli_connect($dblocation,$dbuser,$dbpasswd);
if (!$connection) {
	echo( "<P> В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно. </P>" );
	exit();
}
if (!mysqli_select_db($connection, $dbname)) {
	echo( "<P> В настоящий момент база данных не доступна, поэтому корректное отображение страницы невозможно. .</P>" );
	exit();
}

$servername = "mymon.pkwteile.de";
$query = "SELECT ip, servername, db, err, el FROM `mymon`.`stats`;";
$result = mysqli_query($connection, $query) or die("ERROR!!! :  " .mysqli_error($connection));
$array = mysqli_fetch_assoc($result);

$child_processes = array();
$pid = getmypid();
echo "Parent: ".$pid."\n";
$i = 0;
while (count($child_processes) < 10) {
    $pid = pcntl_fork();
    if ($pid == -1) {
	die("Child process can't be created");
    } elseif ($pid) {
	$child_processes[$pid] = true;
	parent();
    } else {
	child();
    }
}

exit;


function parent() {
	global $i;
	$i++;
}

function child() {
	global $i;
	echo "I= ".$i;
}

