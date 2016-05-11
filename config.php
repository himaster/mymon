<?php

error_reporting(E_ALL);

$isSecure = false;
if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
    $isSecure = true;
}
$REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';

$hostname = $REQUEST_PROTOCOL.'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);

$loglevel = 'debug';

$docroot  = dirname(__FILE__);
