<?php 
    include "menu.php";
?>
<table class="main_table">
        <col span="4">
            <tr>
                <td><b>Server</b></td>
                <td><b>Load Averages</b></td>
                <td><b>Replication</b></td>
                <td><b>500s</b></td>
                <td><b>Elastic</b></td>
            </tr>
<?php
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
        echo "<td>" .$server. "</td>";
		echo "<td><div id='" .$server. "_la'></div></td>";
		echo "<td><div id='" .$server. "_rep'></div></td>";
		echo "<td><div id='" .$server. "_500'></div></td>";
		echo "<td><div id='" .$server. "_elastic'></div></td>";
        include "ajax.php";
    }
	fclose($file);
?>
</table>