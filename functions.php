<?php

function no_injection($str = '')
{
    $str = stripslashes($str);
    $str = trim($str);
    $str = htmlspecialchars($str);
    return $str;
}

function ssh2_return($connection, $query)
{
    $stream = ssh2_exec($connection, $query);
    $error_stream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
    stream_set_blocking($error_stream, true);
    $error_output = stream_get_contents($error_stream);
    stream_set_blocking($stream, true);
    $output = stream_get_contents($stream);
    if (!empty($error_output)) {
        return "Timeout";
    } else {
        return $output;
    }
}

function common_log($logmsg)
{
    $date = date('Y-m-d H:i:s (T)');
    $f = fopen('/var/log/mymon/common.txt', 'a');
    if (!empty($f)) {
        fwrite($f, "$date: PID:".getmypid()."  $logmsg\r\n");
        fclose($f);
    }
}

function console_log($data)
{
    if (is_array($data)) {
        $output = "<script>console.log('console log: " .implode(',', $data). "');</script>";
    } else {
        $output = "<script>console.log('console log: " .$data. "');</script>";
    }
    echo $output;
}

function host_scheme()
{
    $isSecure = false;
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        $isSecure = true;
    }
    return $isSecure ? 'https' : 'http';
}

function slackbot($message) {
    if ($slackbotlevel != 'none') {
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
}
