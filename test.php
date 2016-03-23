<?php

function notify( $message ) {
    global $notify;
    echo "\$notify=".$notify;
    if ($notify == 1) return "<script type=\"text/javascript\">notify(\"$message\");</script>";
}

$notify = 1;
$sqlfontcolor = notify("Replication SQL problem")."<font color=\"red\">";

echo $sqlfontcolor;