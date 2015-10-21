<?php
$dblocation = "localhost";
$dbname = "mymon";
$dbuser = "mymon";
$dbpasswd = "eiGo7iek";
$db = mysql_connect($dblocation,$dbuser,$dbpasswd);
if (!$db) {
	echo( "<P> В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно. </P>" );
	exit();
}
if (!mysql_select_db($dbname, $db)) {
	echo( "<P> В настоящий момент база данных не доступна, поэтому корректное отображение страницы невозможно. .</P>" );
	exit();
}
?>