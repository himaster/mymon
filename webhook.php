<?php
    putenv('PATH='. getenv('PATH') .':/var/www/netbox.co.mymon/');
    exec('git pull 2>&1', $output);
    $fp = fopen(‘webhook.txt’, ‘w’);
    $text = print_r($_POST, 1);
    fwrite($fp, $text);
    fclose($fp);
    var_dump($output);
?>
