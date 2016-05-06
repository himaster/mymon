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

if (!$connection = ssh2_connect($_GET["serverip"], 22)) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 501 Internal Server Error', true, 500);
    die("Connection error!");
}
ssh2_auth_pubkey_file($connection, 'root', '/var/www/netbox.co/mymon/id_rsa.pub', '/var/www/netbox.co/mymon/id_rsa', '');

?>

<a href="http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) ?>">
    <div class="left_button" id="back_button">
        <img src="images/back.png">
    </div>
</a>
<div class="textstyle">

<?php

$str = ssh2_return($connection, "cat /var/log/500.errs");
echo nl2br($str);

?>

</div>