<?php
include "backbutton.php";
?>

<a href="index.php?task=slow_querys">
    <div class="left_button2" id="back_button">
        <img src="images/sql.png">
    </div>
</a>
<table class="main_table table-striped" id="users_table">
    <col span="5">
    <tr class="title">
        <td class="uid">ID</td>
        <td class="login">IP Addr</td>
        <td class="email">Amount</td>
        <?php if ($isAdmin) { ?>
            <td class="approvied">Ban</td>
        <?php } ?>
    </tr>
<?php
$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");
$result = $dbconnection->query("SELECT `bps`.`id`, `bps`.`amount`, `bps`.`ipaddr`,
                                    `wl`.`ip` IS NOT NULL AS `whitelisted`, `bl`.`ip` IS NOT NULL AS `blacklisted`
                                FROM `mymon`.`botips` AS `bps`
                                LEFT JOIN `firewall`.`whitelist` AS `wl`
                                ON (`bps`.`ipaddr` = `wl`.`ip`)
                                LEFT JOIN `firewall`.`blacklist` AS `bl`
                                ON (`bps`.`ipaddr` = `bl`.`ip`)
                                ORDER BY `bps`.`amount`
                                DESC;") or die($dbconnection->error);
while ($row_ip = $result->fetch_assoc()) {
?>
    <tr>
        <td class="uid">
            <?php echo trim($row_ip['id']) ?> 
        </td>
        <td class="login">
            <?php
            $link = trim($row_ip['ipaddr']);
            if ($row_ip['whitelisted'] === '1') {
                echo "<a href='/?task=whois&ip=".$row_ip['ipaddr']."'><font color='green'>".$link."</font></a>";
            } else if (CIDRCheck($link, '66.249.64.0/19')) {
                echo "<a href='/?task=whois&ip=".$row_ip['ipaddr']."'><font color='gray'>".$link."</font></a>";
            } else {
                echo "<a href='/?task=whois&ip=".$row_ip['ipaddr']."'>".$link."</a>";
            }
            ?>
        </td>
        <td class="email">
            <?php
            if (($row_ip['amount'] > 3000) and ($row_ip['amount'] < 10000)) {
                echo "<font color='orange'>".trim($row_ip['amount'])."</font>";
            } else if ($row_ip['amount'] > 10000) {
                echo "<font color='red'>".trim($row_ip['amount'])."</font>";
            } else {
                echo trim($row_ip['amount']);
            }
            ?>
        </td>
        <?php if ($isAdmin) { ?>
        <td>
            <input type="checkbox" <?php echo ($row_ip['blacklisted'] == 1) ? "checked" : ""; ?>
                   onchange="javascript: if (this.checked) ban_ip('<?php echo $row_ip['ipaddr']; ?>');
                                         else unban_ip('<?php echo $row_ip['ipaddr']; ?>');" \>

        </td>
        <?php } ?>
    </tr>
<?php
} ?>
</table>
<div id="status_div" class="status_bar"></div>
