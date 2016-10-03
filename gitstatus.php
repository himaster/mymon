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
$result = $dbconnection->query("SELECT `st`.`servername`,
                                       `st`.`ip`,
                                       `st`.`master_repo`,
                                       `st`.`test_repo`,
                                       IF(`st`.`master_repo`=`master_temp`.`master_repo`,'0','1') AS `master_uniq`,
                                       IF(`st`.`test_repo`=`test_temp`.`test_repo`,'0','1') AS `test_uniq`
                                       FROM `stats` AS `st`
                                JOIN
                                    (SELECT `test_repo`, count(*) AS `count`
                                     FROM `stats`
                                     WHERE `test_repo` IS NOT NULL
                                     GROUP BY `test_repo`
                                     ORDER BY `count`
                                     DESC
                                     LIMIT 1) AS `test_temp`
                                JOIN
                                    (SELECT `master_repo`, count(*) AS `count`
                                     FROM `stats`
                                     WHERE `master_repo` IS NOT NULL
                                     GROUP BY `master_repo`
                                     ORDER BY `count`
                                     DESC
                                     LIMIT 1) AS `master_temp`
                                WHERE `st`.`test_repo` IS NOT NULL AND git=1;") or die($dbconnection->error);
while ($row_ip = $result->fetch_assoc()) {
?>
    <tr>
        <td class="hostname">
            <?php echo trim($row_ip['servername']);?>
        </td>
        <td class="git_master">
            <?php if ($isAdmin){ echo "<a href=# onClick=\"gitpull('".$row_ip['ip']."', 'prod')\">";}
                  if ($row_ip['master_uniq']){ echo "<font color=red>";}
                  echo trim($row_ip['master_repo']);
                  if ($row_ip['master_uniq']){ echo "</font>";}
                  if ($isAdmin){ echo "</a>";}
            ?>
        </td>
        <td class="git_test">
            <?php if ($isAdmin){ echo "<a href=# onClick=\"gitpull('".$row_ip['ip']."', 'dev')\">";}
                  echo trim($row_ip['test_repo']);
                  if ($isAdmin){ echo "</a>";}
            ?>
        <td>
    </tr>
<?php
} ?>
</table>
<div id="status_div" class="status_bar"></div>
