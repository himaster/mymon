<?php
    print_r($_GET);
    putenv('PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin:/root/bin:/var/www/confogen/');
    exec('cd /var/www/netbox.co/mymon/ && git fetch --all && git reset --hard github/master && git pull 2>&1', $output1);
    print_r($output1."<p>");
    exec('cd /var/www/netbox.co/mymon.test/ && git fetch --all && git reset --hard github/test && git pull 2>&1', $output2);
    print_r($output2);
