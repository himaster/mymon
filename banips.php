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

<a href="index.php?task=gitstatus">
    <div class="left_button" id="left_button4">
        <img src="images/git.png" title="Git repositories status">
    </div>
</a>
<table class="main_table table-striped" id="users_table">
    <col span="5">
    <tr class="title">
        <td class="uip">IP addr</td>
        <td class="comment">Comment</td>
        <td class="time">Time</td>
        <?php if ($isAdmin) { ?>
            <td class="ban">Ban</td>
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
        <td class="uip">
            <?php echo "<a href='/?task=whois&ip=".trim($row_ip['ip'])."'>".trim($row_ip['ip'])."</a>"; ?>
        </td>
        <td class="comment">
            <?php echo trim($row_ip['comment']); ?>
        </td>
        <td class="time">
            <?php echo trim($row_ip['time']); ?>
        </td>
        <?php if ($isAdmin) { ?>
        <td class="ban">
                <input type="button"
                       class="ban_button"
                       value="x"
                       onclick="javascript: unban_ip('<?php echo $row_ip['ip']; ?>');
                                            window.setTimeout(location.reload(), 2000);">
            </div>
        <td>
        <?php } ?>
    </tr>
<?php
} ?>
</table>
<div id="status_div" class="status_bar"></div>
