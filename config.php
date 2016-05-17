<?php

error_reporting(E_ALL);

$isSecure = false;
if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] == 443)) {
    $isSecure = true;
}
$REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';

if (array_key_exists('HTTP_HOST', $_SERVER)) {
    $hostname = $REQUEST_PROTOCOL.'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
} else {
    $hostname = $REQUEST_PROTOCOL.'://mymon.pkwteile.de';
}

$loglevel = 'debug';

$docroot  = dirname(__FILE__);
