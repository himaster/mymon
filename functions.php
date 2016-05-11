<?php

$isSecure = false;
if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
    $isSecure = true;
}
$REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';

$hostname = gethostname();

$loglevel = 'debug';

$docroot  = dirname(__FILE__);

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
