<?php 
    include "menu.php";
?>
<table class="main_table">
        <col span="5">
            <tr class="title">
                <td>Server</td>
                <td>Load Averages</td>
                <td>Replication</td>
                <td>500s</td>
                <td>Elastic</td>
                <td>Locks</td>
            </tr>
<?php
    if (ob_get_level() == 0) ob_start();
    echo str_repeat(' ',1024*128);
    flush();
    ob_flush();
    
    $result = $dbconnection->query("SELECT ip, servername, db, mysql, err, el FROM `mymon`.`stats`;") or die($dbconnection->error());
    while($array = $result->fetch_assoc()) {
        $serverip = $array["ip"];
        $server = $array["servername"];
        $errs = $array["err"];
        $elastic = $array["el"];
        $db = $array["db"];
        $mysql = $array["mysql"];
        $serverdb = $server . "_db";
		echo "<tr>";
        echo "<td>" .$server. "</td>";
		echo "<td id='" .$server. "_la'></td>";
		echo "<td id='" .$server. "_rep'></td>";
		echo "<td id='" .$server. "_500'></td>";
		echo "<td id='" .$server. "_elastic'></td>";
        echo "<td id='" .$server. "_locks'></td>";
        echo "<script>";
            echo "$(document).ready(function(){";
                echo "show(\"$serverip\", \"$server\", \"la\");";
                echo "setInterval('show(\"$serverip\", \"$server\", \"la\")',10000);";
                if ($db == 1) { 
                    echo "show(\"$serverip\", \"$server\", \"rep\");";
                    echo "setInterval('show(\"$serverip\", \"$server\", \"rep\")',10000);";
                }
                if ($errs == 1) {
                    echo "show(\"$serverip\", \"$server\", \"500\");";
                    echo "setInterval('show(\"$serverip\", \"$server\", \"500\")',10000);";
                }
                if ($elastic == 1) {
                    echo "show(\"$serverip\", \"$server\", \"elastic\");";
                    echo "setInterval('show(\"$serverip\", \"$server\", \"elastic\")',10000);";
                }
                if ($mysql == 1) {
                    echo "show(\"$serverip\", \"$server\", \"locks\");";
                    echo "setInterval('show(\"$serverip\", \"$server\", \"locks\")',10000);";
                }
            echo "});";
        echo "</script>";
    }
	$dbconnection->close();
?>
</table>