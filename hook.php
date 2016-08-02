<?php
	print_r("Master<p>");
    putenv('PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin:/root/bin:/var/www/confogen/');
    exec('cd /var/www/netbox.co/mymon/ && git fetch --all && git reset --hard origin/master && git pull origin master', $output1);
    print_r($output1."<p>");
    exec('cd /var/www/netbox.co/mymon.test/ && git fetch --all && git reset --hard origin/test && git pull origin test', $output2);
    print_r($output2);
