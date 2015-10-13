<?php 
    include "menu.php";
?>
<table border="2" align="center" bgcolor="lightgray">
        <col span="4">
            <tr>
                <td width="80"><b>Server</b></td>
                <td width="120"><b>Load Averages</b></td>
                <td width="160"><b>Replication</b></td>
                <td width="40"><b>500s</b></td>
                <td width="60"><b>Elastic</b></td>
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
        echo "<td style='text-align: left'><b><font color='black'>" .$server. "</font></b></td>";
		echo "<td><div id='" .$server. "_la'></div></td>";
		echo "<td><div id='" .$server. "_rep'></div></td>";
		echo "<td><div id='" .$server. "_500'></div></td>";
		echo "<td><div id='" .$server. "_elastic'></div></td>";
        include "ajax.php";
    }
	fclose($file);
?>
</table>