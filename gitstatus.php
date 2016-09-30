<?php
if ($_SERVER["SCRIPT_NAME"] != "/index.php") {
    die();
}

backButton("/");
?>

<a href="index.php?task=botips">
    <div class="left_button" id="left_button2">
        <img src="images/botips.png" title="Bot IPs">
    </div>
</a>

<a href="index.php?task=slow_querys">
    <div class="left_button" id="left_button3">
        <img src="images/sql.png" title="MySQL slow scripts">
    </div>
</a>

<a href="index.php?task=banips">
    <div class="left_button" id="left_button4">
        <img src="images/banip.png" title="Banned IPs">
    </div>
</a>

<table class="main_table table-striped" id="users_table">
    <col span="4">
    <tr class="title">
        <td class="hostname">Server</td>
        <td class="git_master">fuel.prod</td>
        <td class="git_test">fuel.dev</td>
    </tr>
<?php
$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");
$result = $dbconnection->query("SELECT servername, ip, master_repo, test_repo FROM `stats` WHERE git=1;") or die($dbconnection->error);
while ($row_ip = $result->fetch_assoc()) {
?>
    <tr>
        <td class="hostname">
            <?php echo trim($row_ip['servername']);?>
        </td>
        <td class="git_master">
            <?php if ($isAdmin){ echo "<a href=index.php?task=gitpull&tag=prod&ip=".$row_ip['ip'].">";}
                  echo trim($row_ip['master_repo']);
                  if ($isAdmin){ echo "</a>";}
            ?>
        </td>
        <td class="git_test">
            <?php if ($isAdmin){ echo "<a href=index.php?task=gitpull&tag=dev&ip=".$row_ip['ip'].">";}
                  echo trim($row_ip['test_repo']);
                  if ($isAdmin){ echo "</a>";}
            ?>
        <td>
    </tr>
<?php
} ?>
</table>
<div id="status_div" class="status_bar"></div>
