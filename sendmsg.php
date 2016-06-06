<?php

$umessage = no_injection($_POST['umessage']);
foreach ($_POST['uselect'] as $ulogin) {
    $result = $dbconnection->query("INSERT INTO `mymon`.`messages` (`message`, `sender`, `receiver`)
                                    VALUES ('$umessage', '$uid', '$ulogin');") or
    die("Error occured".$dbconnection->error);
}
echo "Message sent.";
