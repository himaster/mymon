<?php

if ($_SERVER["SCRIPT_NAME"] != "/index.php") {
    die();
}

$umessage = no_injection($_POST['umessage']);
foreach ($_POST['uselect'] as $ulogin) {
    $result = $dbconnection->query("INSERT INTO $db.`messages` (`message`, `sender`, `receiver`)
                                    VALUES ('$umessage', '$uid', '$ulogin');") or
    die("Error occured".$dbconnection->error);
}
echo "Message sent.";
