<?php

require_once 'config.php';

$error_log  = '/var/log/mymon/errors.txt';
$common_log = '/var/log/mymon/common.txt';

function no_injection($str = '') {
    $str = stripslashes($str);
    $str = trim($str);
    $str = htmlspecialchars($str);
    return $str;
}

function ssh2_return($connection, $query) {
    $stream = ssh2_exec($connection, $query);
    $error_stream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
    stream_set_blocking($error_stream, true);
    $error_output = stream_get_contents($error_stream);
    stream_set_blocking($stream, true);
    $output = stream_get_contents($stream);
    if (!empty($error_output)) {
        return $error_output;
    } else {
        return $output;
    }
}

function common_log($logmsg) {
    global $common_log;
    $date = date('Y-m-d H:i:s (T)');
    $f = fopen($common_log, 'a');
    if (!empty($f)) {
        fwrite($f, "$date: PID:".getmypid()."  $logmsg\r\n");
        fclose($f);
    }
}

function console_log($data) {
    if (is_array($data)) {
        $output = "<script>console.log('console log: " .implode(',', $data). "');</script>";
    } else {
        $output = "<script>console.log('console log: " .$data. "');</script>";
    }
    echo $output;
}

function host_scheme() {
    $isSecure = false;
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        $isSecure = true;
    }
    return $isSecure ? 'https' : 'http';
}

function slackbot($message) {
    global $slackbotlevel;
    global $dbhost;
    global $dbusername;
    global $dbpass;
    global $database;

    $dbconnection = new mysqli($dbhost, $dbusername, $dbpass, $database) or die("Mysql error.".$dbconnection->connect_errno."\n");
    $starttime = strtotime(date("Y-m-d H:i:s"));
    $lasttime  = strtotime($dbconnection->query("SELECT `timestamp`
                                  FROM $database.`slack_messages`;")->fetch_row()[0]);
    if ($starttime - $lasttime > 300 and $slackbotlevel == "full") {
        $dbconnection->query("UPDATE $database.`slack_messages` SET `test` = NOT `test`;");
        $channel = "#sys-admins";
        $username = "mymon-bot";
        $icon_url = "https://mymon.pkwteile.de/images/mymon_mini.png";
        $slackhook = "https://hooks.slack.com/services/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";
        $query = "payload={\"channel\": \"$channel\", \"username\": \"$username\", \"text\": \"$message\", \"icon_url\": \"$icon_url\"}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $slackhook);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);
        print_r($result);
    }
    $dbconnection->close();
}

function CIDRCheck($IP, $CIDR) {
    list($net, $mask) = split("/", $CIDR);
    $ip_net = ip2long($net);
    $ip_mask = ~((1 << (32 - $mask)) - 1);
    $ip_ip = ip2long($IP);
    $ip_ip_net = $ip_ip & $ip_mask;
    return ($ip_ip_net == $ip_net);
}

function backButton($href) {
    echo "<a href='".$href."'>";
    echo "<div class='left_button' id='back_button'>";
    echo "    <img src='images/back.png' title='Previous page'>";
    echo "</div>";
    echo "</a>";
}

function not_less_than_zero($val) {
    if ($val < 0) {
        return 0;
    } else {
        return $val;
    }
}

function sigHandler($signo) {
    global $stop_server;
    switch ($signo) {
        case SIGTERM:
            $stop_server = true;
            common_log("SIGTERM stop");
            break;

        case SIGPIPE:
            $stop_server = true;
            common_log("SIGPIPE stop");
            break;

        default:
            break;
    }
}

function errHandler($errno, $errmsg, $filename, $linenum) {
    global $error_log;
    global $servername;
    $date = date('Y-m-d H:i:s (T)');
    $f = fopen($error_log, 'a');
    if (!empty($f)) {
        $filename  = str_replace($_SERVER['DOCUMENT_ROOT'], '', $filename);
        fwrite($f, "$date: server: $servername: $errmsg - $filename - $linenum\r\n");
        fclose($f);
    }
}

function ssh_disconnect() {
    common_log("SSH disconnect");
}

function ssh_ignore() {
    common_log("SSH ignore");
}

function ssh_debug() {
    common_log("SSH debug");
}

function ssh_macerror() {
    common_log("SSH macerror");
}

function var_bump($variable) {
    ob_start();
    var_dump($variable);
    $res = ob_get_clean();
    return $res;
}
