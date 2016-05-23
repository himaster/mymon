<?php

$database = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon")
                  or die($$mysql_conname->connect_errno."\n");
$slackbotlevel = 'full';
function slackbot($message)
{
    global $slackbotlevel;
    global $database;
    
    $starttime = strtotime(date("Y-m-d H:i:s"));
    $lasttime = strtotime($database->query("SELECT `timestamp`
                                  FROM `mymon`.`slack_messages`;")->fetch_row()[0]);
    if ($starttime - $lasttime > 60 and $slackbotlevel == "full") {
        $database->query("UPDATE `mymon`.`slack_messages` SET `test` = NOT `test`;");
        echo $message;
    }
}

slackbot("Test");
