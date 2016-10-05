<?php

if ($_SERVER["SCRIPT_NAME"] != "/index.php") {
    die();
}

backButton("/");
?>
<table class="main_table table-striped" id="users_table">
    <col span="5">
    <tr class="title">
        <td class="uid">UID</td>
        <td class="login">Login</td>
        <td class="email">Email</td>
        <td class="role">Role</td>
        <td class="approvied">Approvied</td>
        <td class="delete">Delete</td>

    </tr>
<?php
$dbconnection = new mysqli($host, $username, $pass, $database) or die("Mysql error.".$dbconnection->connect_errno."\n");
$result = $dbconnection->query("SELECT `id`, `login`, `email`, `approvied`, GROUP_CONCAT(`ur`.`role_id`)
                                AS roles
								FROM $database.`users`
								LEFT JOIN $database.`user_roles` AS `ur` ON (`id` = `ur`.`user_id`)
								GROUP BY `id`;") or die($dbconnection->error);
while ($row_user = $result->fetch_assoc()) {
    $user_roles = explode(",", $row_user['roles']);
?>
    <tr>
        <td class="uid">
            <input id="<?php echo trim($row_user['login']) ?>^uid"
                   type="text"
                   value="<?php echo trim($row_user['id']) ?>"
                   onchange="javascript: users_editor(this.id, this.value);">
        </td>
        <td class="login">
            <input id="<?php echo trim($row_user['login']) ?>^login"
                   type="text"
                   value="<?php echo trim($row_user['login']) ?>"
                   onchange="javascript: users_editor(this.id, this.value); ">
        </td>
        <td class="email">
            <input id="<?php echo trim($row_user['login']) ?>^email"
                   type="text"
                   value="<?php echo trim($row_user['email']) ?>"
                   onchange="javascript: users_editor(this.id, this.value); ">
        </td>
        <td>
            <select id="<?php echo trim($row_user['login']) ?>^role"
                    multiple
                    onchange="javascript: users_editor(this.id, implode(',', $(this).val())); ">
    <?php
    foreach ($roles as $key => $value) {
        if (in_array($key, $user_roles)) {
            $selected = "selected='selected'";
        } else {
            $selected = "";
        }
        echo "<option value=\"$key\" $selected>$value\n";
    }
    ?>
            </select>
        </td>
        <td><input id="<?php echo trim($row_user['login']) ?>^approvied" type="checkbox"
<?php if (trim($row_user['approvied']) == 1) {
    echo "checked ";
} ?>
            onchange="javascript: users_editor(this.id, this.checked);">
        </td>
        <td>
            <input type="button"
                   id="<?php echo trim($row_user['id']) ?>"
                   value="x"
                   onclick="javascript: if(confirm('Want to delete user?')) {
                                            user_remove(this.id);
                                        }"
            >
        </td>
    </tr>
<?php
} ?>
</table>
<div id="status_div" class="status_bar"></div>
