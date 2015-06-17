<?php
    putenv('PATH='. getenv('PATH') .':/var/www/netbox.co/mymon/');
    exec('cd /var/www/netbox.co/mymon/ && git pull https://github.com/himaster/mymon.git master 2>&1', $output);
    $fp = fopen('webhook.txt', 'w');
    $text = print_r($_POST, 1);
    fwrite($fp, $text);
    fclose($fp);
    var_dump($output);
?>
