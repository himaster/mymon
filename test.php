<?php

require_once "config.php";
require_once "functions.php";


echo "isSecure='".$isSecure."'\n";
var_dump($_SERVER['HTTPS']);
var_dump($_SERVER['SERVER_PORT']);
