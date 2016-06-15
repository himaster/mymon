<?php

/**
 * 500 File Doc Comment
 *
 * @category Show_500_Errors
 * @package  MyMon
 * @author   himaster <himaster@mailer.ag>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://mymon.pkwteile.de
 */

if (( ! $connection = @ssh2_connect($_GET['serverip'], 22))
        or ( ! @ssh2_auth_pubkey_file($connection, 'root', $docroot.'/id_rsa.pub', $docroot.'/id_rsa', ''))) {
    header($_SERVER['SERVER_PROTOCOL'].' 501 Internal Server Error', true, 500);
    die("Connection error!");
}

backButton("/");

?>

<div class="textstyle">

<?php
if (empty($_GET["page"])) {
	$page = 1;
} else {
	$page = $_GET["page"];
}
echo "<p align=center>";
if ($page > 1) {
	echo "<a href=index.php?task=500err&serverip=".$_GET['serverip']."&page=".($page - 1).">&lt;</a>";
} else {
	echo " ";
}
echo " -".$page."- ";
$str_amount = ssh2_return($connection, "cat /var/log/500err.log");
if ($str_amount > ($page * 10)) {
	echo "<a href=index.php?task=500err&serverip=".$_GET['serverip']."&page=".($page + 1).">&gt;</a><br>";
}
echo "</p>";
$str = ssh2_return($connection, "tail -n +".(($page - 1) * 10)." /var/log/500.errs | head -n 10");
echo nl2br($str);

?>

</div>
