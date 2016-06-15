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

include "backbutton.php";

?>

<div class="textstyle">

<?php
if (empty($_GET["page"])) {
	$page = 0;
} else {
	$page = $_GET["page"];
}
if ($page > 0) {
	echo "<a href=500.php?page=".($page - 1)."> &lt; </a>";
} else {
	echo " ";
}
echo " -".$page."- ";
echo "<a href=500.php?page=".($page + 1).">&gt;</a>\n";
$str = ssh2_return($connection, "tail -n +".($page * 10)." /var/log/500.errs | head -n 11");
echo nl2br($str);

?>

</div>
