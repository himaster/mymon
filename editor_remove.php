<?php

if (!$isAdmin) {
    die("You have not rights.");
}
if (isset($_GET['id'])) {
	$id = $_GET['id'];
} else {
	die("No id received.");
}

if ($result = $dbconnection->query("DELETE FROM `mymon`.`stats`
                                    WHERE `id` = '$id'")) {
    echo "Successfully edited";
} else {
    print_r($dbconnection->error);
}
