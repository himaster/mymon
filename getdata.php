<?php

if ($_SERVER["SCRIPT_NAME"] != "/index.php") {
    die();
}

$rows=array();
$result = $dbconnection->query("SELECT `id`, UNIX_TIMESTAMP(`timestamp`)
        AS `timestamp`, `servername`, `la`, `rep`, `500`, `elastic`, `locks`, `mongo`, `redis`
        FROM `mymon`.`stats`;") or
die($dbconnection->error());
while ($array = $result->fetch_assoc()) {
    $rows["data"][] = $array;
}
$result = $dbconnection->query("SELECT `messages`.`id`, UNIX_TIMESTAMP(`messages`.`timestamp`)
        AS `timestamp`, `messages`.`message`, `users`.`login`
        FROM `mymon`.`messages` JOIN `users`
        WHERE `messages`.`sender` = `users`.`id`
        AND `receiver` = '$uid'
        AND isRead = 0
        AND isDeleted = 0
        LIMIT 1;") or
die($dbconnection->error());
if (mysqli_num_rows($result)>0) {
        $rows["msg"] = $result->fetch_assoc();
}
header("Content-Type: application/json; charset=utf-8 ");
echo json_encode($rows);
