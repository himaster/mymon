<?php

if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
	(!empty($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] === "443")) {
    $isSecure = 1;
} else {
    $isSecure = 0;
}
$REQUEST_PROTOCOL = $isSecure?'https':'http';

if (array_key_exists('HTTP_HOST', $_SERVER)) {
    $hostname = $REQUEST_PROTOCOL.'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
} else {
    $hostname = $REQUEST_PROTOCOL.'://mymon.pkwteile.de';
}

$ssh_callbacks = array('disconnect' => 'ssh_disconnect',
                       'ignore'     => 'ssh_ignore',
                       'debug'      => 'ssh_debug',
                       'macerror'   => 'ssh_macerror');

$balancerip = '88.198.182.148';

# 0 => 'none'
# 1 => 'SQL scripts'
# 3 => 'everything'
$loglevel = 0;

# 'full' or 'none'
$slackbotlevel = 'full';

$docroot  = dirname(__FILE__);

$retry_num   = 10;

$iplistnum = 30;

$dbhost     = "balancer1.pkwteile.de";
$dbusername = "mymon";
$dbpass     = "VeiSh6Uc";
$database   = "mymon";
