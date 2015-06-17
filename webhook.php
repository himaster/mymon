<?php
    putenv('PATH='. getenv('PATH') .':/var/www/netbox.co.mymon/');
    exec('git pull 2>&1', $output);
    var_dump($output);
?>
