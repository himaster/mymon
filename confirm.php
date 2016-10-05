<?php

$login = no_injection($_GET["username"]);
$result = $dbconnection->query("SELECT id FROM $database.`users` WHERE login ='{$login}' LIMIT 1;") or die($dbconnection->error);
$uid = $result->fetch_assoc()['id'];
$result = $dbconnection->query("SELECT `id`, `name` FROM $database.`roles`") or die($dbconnection->error);
while ($row = $result->fetch_assoc()) {
    if ($_GET[$row['name']] == "on") {
        $rid = $row['id'];
        $dbconnection->query("INSERT INTO $database.`user_roles`(`user_id`, `role_id`)
                              VALUES ('$uid', '$rid');") or die($dbconnection->error);
    }
}
$result = $dbconnection->query("UPDATE $database.`users`
                                SET approvied = '1'
                                WHERE login = '$login';") or die($dbconnection->error);
$result = $dbconnection->query("SELECT email
                                FROM $database.`users`
                                WHERE login = '$login';") or die($dbconnection->error);
$msg = wordwrap("Hi! Your login ($login) just confirmed. Try to login on ".$hostname, 70);
$headers =  "From: mymon@netbox.co\r\nReply-To: himaster@mailer.ag\r\n";
mail($result->fetch_assoc()['email'], "Mymon registration", $msg, $headers);
echo "<p>Profile updated successfully.";
