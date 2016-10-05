<?php

if ($_SERVER["SCRIPT_NAME"] != "/index.php") {
    die();
}

if (!$isAdmin) {
    die("You have not rights.");
}
$username = $_GET['username'];
$columnname = $_GET['columnname'];
$val = $_GET['val'];
if ($columnname == "role") {
    $result = $dbconnection->query("SELECT `id`
                                    FROM $database.`users`
                                    WHERE `login` = '$username'");
    $user_id = $result->fetch_assoc()['id'];
    $query = "DELETE FROM $database.`user_roles`
              WHERE `user_id` = '$user_id;'; ";
    $roles_array = explode(',', $val);
    foreach ($roles_array as $item) {
        $query .= "INSERT INTO $database.`user_roles`(`user_id`, `role_id`)
                   VALUES ('$user_id', '$item');";
    }
} else {
    $query = "UPDATE $database.`users`
              SET `$columnname` = '$val'
              WHERE `login` = '$username'";
}
if ($result = $dbconnection->multi_query($query)) {
    echo "Successfully edited.";
} else {
    var_dump($dbconnection->error);
}
