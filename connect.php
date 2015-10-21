<?php
$dblocation = "localhost";
$dbname = "mymon";
$dbuser = "mymon";
$dbpasswd = "eiGo7iek";
$db = mysqli_connect($dblocation,$dbuser,$dbpasswd);
if (!$db) {
	echo( "<P> В настоящий момент сервер базы данных не доступен, поэтому корректное отображение страницы невозможно. </P>" );
	exit();
}
if (!mysqli_select_db($db, $dbname)) {
	echo( "<P> В настоящий момент база данных не доступна, поэтому корректное отображение страницы невозможно. .</P>" );
	exit();
}
?>