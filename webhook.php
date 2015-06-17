<?php
    putenv('PATH='. getenv('PATH') .':/var/www/netbox.co/mymon/');
    exec('cd /var/www/netbox.co/mymon/ && git pull https://github.com/himaster/mymon.git master 2>&1', $output);
    exec('cd /var/www/netbox.co/mymon.test/ && git pull https://github.com/himaster/mymon.git dev 2>&1', $output);
?>
