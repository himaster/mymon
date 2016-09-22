<?php
backButton("/");
?>

<a href="index.php?task=slow_querys">
    <div class="left_button" id="left_button2">
        <img src="images/sql.png" title="MySQL slow scripts">
    </div>
</a>
<table class="main_table table-striped" id="users_table">
    <col span="5">
    <tr class="title">
        <td class="uid">IP addr</td>
        <td class="comment">Comment</td>
        <td class="time">Time</td>
        <?php if ($isAdmin) { ?>
            <td class="approvied">Ban</td>
        <?php } ?>
    </tr>
<?php
$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "firewall") or die($dbconnection->connect_errno."\n");
$result = $dbconnection->query("SELECT *
                                FROM `firewall`.`blacklist`
                                ORDER BY `firewall`.`blacklist`.`ip`
                                ASC;") or die($dbconnection->error);
while ($row_ip = $result->fetch_assoc()) {
?>
    <tr>
        /*<td class="uid">
            <?php echo trim($row_ip['id']) ?>
        </td>*/
        <td class="ip">
            <?php echo "<a href='/?task=whois&ip=".trim($row_ip['ip'])."'>".trim($row_ip['ip'])."</a>"; ?>
        </td>
        <td class="comment">
            <?php echo trim($row_ip['comment']); ?>
        </td>
        <td class="time">
            <?php echo trim($row_ip['time']); ?>
        </td>
        <?php if ($isAdmin) { ?>
        <td>
                <input type="button"
                       class="ban_button"
                       value="ban"
                       onclick="javascript: unban_ip('<?php echo $row_ip['ip']; ?>');">
            </div>
        <td>
        <?php } ?>
    </tr>
<?php
} ?>
</table>
<div id="status_div" class="status_bar"></div>
