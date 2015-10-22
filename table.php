<?php 
    include "menu.php";
    include_once("connect.php");
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
    $query = "SELECT ip, servername, db, err, el FROM `mymon`.`stats`;";
    $result = mysql_query($query, $db) or die("ERROR!!! :  " .mysql_error());
    while($array = mysql_fetch_assoc($result)) {
        $serverip = $array["ip"];
        $server = $array["servername"];
        $errs = $array["err"];
        $elastic = $array["el"];
        $db = $array["db"];
        $serverdb = $server . "_db";
        die("test");
		echo "<tr>";
        echo "<td>" .$server. "</td>";
		echo "<td><div id='" .$server. "_la'></div></td>";
		echo "<td><div id='" .$server. "_rep'></div></td>";
		echo "<td><div id='" .$server. "_500'></div></td>";
		echo "<td><div id='" .$server. "_elastic'></div></td>";
        echo "<script>";
            echo "$(document).ready(function(){";
                echo "show(\"$serverip\", \"$server\", \"la\");";
                echo "setInterval('show(\"$serverip\", \"$server\", \"la\")',10000);";
                if (isset($db)) { 
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
            echo "});";
        echo "</script>";
    }
	mysql_close($db);
?>
</table>