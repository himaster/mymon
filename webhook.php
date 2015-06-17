<?php
    putenv('PATH='. getenv('PATH') .':/var/www/netbox.co.mymon/');
    exec('git pull 2>&1', $output);
    print_r($_POST);
    var_dump($output);
?>
