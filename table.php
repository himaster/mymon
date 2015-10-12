<?php 
    include "menu.php";
    include "table_header.html";

    if (ob_get_level() == 0) ob_start();
    echo str_repeat(' ',1024*128);
    flush();
    ob_flush();
    $file = fopen("./servers.conf", "r");
    while(! feof($file)) {
        $line = fgets($file);
        if ($line[0] == '#') {
			continue;
		}
        $array = explode(" ", $line);
        $serverip = $array[0];
        $server = $array[1];
        $errs = $array[2];
        $elastic = $array[3];
        $db = $array[4];
        $serverdb = $server . "_db";
		echo "<tr>";
        echo "<td style='text-align: left'><b><a href='http://netbox.co/mymon/testgraph.php?serverip=" .$serverip. "' target='_blank' style='text-decoration: none;'><font color='black'>" .$server. "</font></b></td>";
		echo "<td><div id='" .$server. "_la'></div></td>";
		echo "<td><div id='" .$server. "_rep'></div></td>";
		echo "<td><div id='" .$server. "_500'></div></td>";
		echo "<td><div id='" .$server. "_elastic'></div></td>";
        include "ajax.php";
    }
	fclose($file);
?>
</table>