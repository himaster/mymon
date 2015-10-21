<?php
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
?>