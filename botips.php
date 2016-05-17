<a href="<?php echo $hostname ?>">
    <div class="left_button" id="back_button">
        <img src="images/back.png">
    </div>
</a>
<table class="main_table table-striped" id="users_table">
    <col span="5">
    <tr class="title">
        <td class="uid">ID</td>
        <td class="login">IP Addr</td>
        <td class="email">Amount</td>
        <td class="approvied">Ban</td>
    </tr>
<?php
$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");
$result = $dbconnection->query("SELECT `id`, `amount`, `ipaddr`
								FROM `mymon`.`botips`;") or die($dbconnection->error);
while ($row_ip = $result->fetch_assoc()) {
?>
    <tr>
        <td class="uid">
            <?php echo trim($row_ip['id']) ?> 
        </td>
        <td class="login">
            <?php echo trim($row_ip['ipaddr']) ?>
        </td>
        <td class="email">
            <?php echo trim($row_ip['amount']) ?>
        </td>
    </tr>
<?php
} ?>
</table>
<div id="status_div" class="status_bar"></div>
