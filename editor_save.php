<?php

if (!$isAdmin) {
    die("You have not rights.");
}
$servername = $_GET['servername'];
$columnname = $_GET['columnname'];
$val = $_GET['val'];
if ($result = $dbconnection->query("UPDATE `mymon`.`stats`
                                    SET `$columnname` = '$val'
                                    WHERE `servername` = '$servername'")) {
    echo "Successfully edited";
} else {
    print_r($dbconnection->error);
}
