<?php 
    include "menu.php";
?>
<table class="main_table">
        <col span="4">
            <tr class="title">
                <td>Server</td>
                <td>Load Averages</td>
                <td>Replication</td>
                <td>500s</td>
                <td>Elastic</td>
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
        echo "<script>";
            echo "$(document).ready(function(){";
                echo "show(\"$serverip\", \"$server\", \"la\");";
                echo "setInterval('show(\"$serverip\", \"$server\", \"la\")',15000);";
                if (isset($db)) { 
                    echo "show(\"$serverip\", \"$server\", \"rep\");";
                    echo "setInterval('show(\"$serverip\", \"$server\", \"rep\")',15000);";
                }
                if ($errs == 1) {
                    echo "show(\"$serverip\", \"$server\", \"500\");";
                    echo "setInterval('show(\"$serverip\", \"$server\", \"500\")',15000);";
                }
                if ($elastic == 1) {
                    echo "show(\"$serverip\", \"$server\", \"elastic\");";
                    echo "setInterval('show(\"$serverip\", \"$server\", \"elastic\")',15000);";
                }
            echo "});";
        echo "</script>";
    }
	fclose($file);
?>
</table>