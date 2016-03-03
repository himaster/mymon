<?php

include "functions.php";

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, '127.0.0.1', 8000);
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_listen($socket);
