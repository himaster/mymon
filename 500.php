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

require_once 'functions.php';

if (( ! $connection = @ssh2_connect($_GET['serverip'], 22))
        or ( ! @ssh2_auth_pubkey_file($connection, 'root', $docroot.'/id_rsa.pub', $docroot.'/id_rsa', ''))) {
    header($_SERVER['SERVER_PROTOCOL'].' 501 Internal Server Error', true, 500);
    die("Connection error!");
}

?>

<a href="http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) ?>">
    <div class="left_button" id="back_button">
        <img src="images/back.png">
    </div>
</a>
<div class="textstyle">

<?php

$str = ssh2_return($connection, 'cat /var/log/500.errs');
echo nl2br($str);

?>

</div>