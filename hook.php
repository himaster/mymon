<?php
	print_r("Master");
    putenv('PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin:/root/bin:/var/www/confogen/');
    exec('cd /var/www/netbox.co/mymon/ && git pull https://github.com/himaster/mymon.git master 2>&1', $output);
    print_r($output."\n");
    exec('cd /var/www/netbox.co/mymon.test/ && git pull https://github.com/himaster/mymon.git test 2>&1', $output);
    print_r($output);
