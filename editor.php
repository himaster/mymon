<?php
if ($_SERVER["SCRIPT_NAME"] != "/index.php") {
    die();
}
backButton("http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
?>
<table class="main_table table-striped">
    <col span="5">
    <tr class="title">
        <td class="serv"> server</td>
        <td class="ipaddr">IP address</td>
        <td class="role">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;server&nbsp;role&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td class="rep">replication</td>
        <td class="loc">locks</td>
        <td class="500">500s</td>
        <td class="el">elastic</td>
        <td class="mon">mongo</td>
        <td class="red">redis</td>
        <td class="color">color</td>
        <td class="color">x</td>
    </tr>
<?php
    $dbconnection = new mysqli($dbhost, $dbusername, $dbpass, $database) or die("Mysql error.".$dbconnection->connect_errno."\n");
    $query  = "SELECT `id`, `servername`, `ip`, `role`, `db`, `mysql`, `err`, `el`, `mon`, `red`, `color`
               FROM $database.`stats`
               ORDER BY LEFT(`servername`,3),
                        CAST( SUBSTRING(`servername`, INSTR(`servername`,  '-' ) +1 ) AS UNSIGNED),
                        `servername`;";
    $result = $dbconnection->query($query) or die($dbconnection->error);
while ($row_user = $result->fetch_assoc()) { ?>
    <tr>
        <td class="serv">
            <input  id="<?php echo trim($row_user['servername']) ?>^servername"
                    type="text"
                    value="<?php echo trim($row_user['servername']) ?>"
                    onchange="javascript: editor(this.id, this.value);">
        </td>
        <td class="ipaddr">
            <input  id="<?php echo trim($row_user['servername']) ?>^ip"
                    type="text"
                    value="<?php echo trim($row_user['ip']) ?>"
                    onchange="javascript: editor(this.id, this.value); ">
        </td>
        <td class="role">
            <select id="<?php echo trim($row_user['servername']) ?>^role"
                    onchange="javascript: editor(this.id, this.value); ">
                <?php
                foreach ($roles as $key => $value) {
                    if ($key == intval($row_user['role'])) {
                        $selected = "selected='selected'";
                    } else {
                        $selected = "";
                    }
                    echo "<option value=\"$key\" $selected>$value\n";
                }
                ?>
            </select>
        </td>
        <td class="rep">
            <input  id="<?php echo trim($row_user['servername']) ?>^db"
                    type="checkbox"
                                    <?php
                                    if (trim($row_user['db']) == 1) {
                                        echo "checked ";
                                    }
                                    ?>
                    onchange="javascript: editor(this.id, this.checked);">
        </td>
        <td class="loc">
            <INPUT  id="<?php echo trim($row_user['servername']) ?>^mysql"
                    type="checkbox"
                                    <?php
                                    if (trim($row_user['mysql']) == 1) {
                                        echo "checked ";
                                    }
                                    ?>
                    onchange="javascript: editor(this.id, this.checked);">
        </td>
        <td class="500">
            <input  id="<?php echo trim($row_user['servername']) ?>^err"
                    type="checkbox"
                                    <?php
                                    if (trim($row_user['err']) == 1) {
                                        echo "checked ";
                                    }
                                    ?>
                    onchange="javascript: editor(this.id, this.checked);">
        </td>
        <td class="el">
            <input  id="<?php echo trim($row_user['servername']) ?>^el"
                    type="checkbox"
                                    <?php
                                    if (trim($row_user['el']) == 1) {
                                        echo "checked ";
                                    }
                                    ?>
                    onchange="javascript: editor(this.id, this.checked);">
        </td>
        <td class="mon">
            <input  id="<?php echo trim($row_user['servername']) ?>^mon"
                    type="checkbox"
                                    <?php
                                    if (trim($row_user['mon']) == 1) {
                                        echo "checked ";
                                    }
                                    ?>
                    onchange="javascript: editor(this.id, this.checked);">
        </td>
        <td class="red">
            <input  id="<?php echo trim($row_user['servername']) ?>^red"
                    type="checkbox"
                                    <?php
                                    if (trim($row_user['red']) == 1) {
                                        echo "checked ";
                                    }
                                    ?>
                    onchange="javascript: editor(this.id, this.checked);">
        </td>
        <td class="color">
            <input  id="<?php echo trim($row_user['servername']) ?>^color"
                    type="color"
                    value="<?php echo trim($row_user['color']) ?>"
                    oninput="javascript: editor(this.id, this.value);">
        </td>
        <td class="remove">
            <a href="javascript: editor_remove(<?php echo $row_user['id'] ?>);">x</a>
        </td>

    </tr>
<?php
}
?>
</table>
<div id="status_div" class="status_bar">
</div>
