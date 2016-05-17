<?php

require_once 'config.php';
require_once 'functions.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE);
$serverip      = "88.198.182.148";
$ssh_callbacks = array('disconnect' => 'ssh_disconnect',
                   'ignore'     => 'ssh_ignore',
                   'debug'      => 'ssh_debug',
                   'macerror'   => 'ssh_macerror');

if (( ! $connection = ssh2_connect($serverip, 22, $ssh_callbacks))
        or ( ! ssh2_auth_pubkey_file($connection, 'root', $docroot.'/id_rsa.pub', $docroot.'/id_rsa', ''))) {
    die("SSH connection error");
}
$str = ssh2_return($connection, "tail -n 1000000 /var/log/nginx/access.log |
                                 awk '{print $1}' |
                                 sort |
                                 uniq -c |
                                 sort -n |
                                 tail -n30");
$i = 0;
foreach (explode("\n", $str) as $cLine) {
    echo $cLine."\n";
    continue;
    $i++;
    $cLine = trim($cLine);
    list($cAmount, $cIPaddr) = explode(' ', "$cLine ");
    $ipaddrarray[$i]['amount'] = $cAmount;
    $ipaddrarray[$i]['ipaddr'] = $cIPaddr;
    echo $i." - ".$ipaddrarray[$i]['amount']." - ".$ipaddrarray[$i]['ipaddr']."\n";
}
