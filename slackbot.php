<?php

/* Slackbot
*/

function slackbot($message) {
    $channel = "#sys-admins";
    $username = "mymon-bot";
    $icon_url = "https://mymon.pkwteile.de/images/mymon_mini.png";
    $slackhook = "https://hooks.slack.com/services/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";
    $query = "payload={\"channel\": \"$channel\", \"username\": \"$username\", \"text\": \"$message\", \"icon_url\": \"$icon_url\"}";

    // Set the cURL options
    //die($query);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $slackhook);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute post
    $result = curl_exec($ch);

    // Close connection
    curl_close($ch);
    print_r($result);
}

slackbot(";)");
