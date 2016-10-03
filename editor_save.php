<?php
if ($_SERVER["SCRIPT_NAME"] != "/index.php") {
    die();
}

if (!$isAdmin) {
    die("You have not rights.");
}
$servername = $_GET['servername'];
$columnname = $_GET['columnname'];
$val = $_GET['val'];
if ($result = $dbconnection->query("UPDATE $db.`stats`
                                    SET `$columnname` = '$val'
                                    WHERE `servername` = '$servername'")) {
    echo "Successfully edited";
} else {
    print_r($dbconnection->error);
}
