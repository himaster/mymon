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
    $result = $dbconnection->query("SELECT `st`.`servername`, `st`.`ip`, `st`.`db`, `st`.`mysql`, `st`.`err`, `st`.`el` 
                                    FROM `user_roles` AS `ur` 
                                    JOIN `stats` AS `st` 
                                    ON `st`.`role` = `ur`.`role_id` 
                                    WHERE `ur`.`user_id` = {$uid};") or die($dbconnection->error());
    while($array = $result->fetch_assoc()) {
        $serverip = $array["ip"];
        $server = $array["servername"];
        $errs = $array["err"];
        $elastic = $array["el"];
        $db = $array["db"];
        $mysql = $array["mysql"];
        $serverdb = $server . "_db";
		echo "<tr>";
        echo "<td id='" .$server. "_name'>" .$server. "</td>";
		echo "<td id='" .$server. "_la'></td>";
		echo "<td id='" .$server. "_rep'></td>";
		echo "<td id='" .$server. "_500'></td>";
		echo "<td id='" .$server. "_elastic'></td>";
        echo "<td id='" .$server. "_locks'></td>";
    }
	$dbconnection->close();
?>
</table>