<?php 
    include "menu.php";
?>
<table class="main_table">
    <col span="5">
        <tr class="title">
            <td>Server</td>
            <?php 
            if ($ula == "1")  echo "<td class=\"la\">Load Averages</td>";
            if ($urep == "1") echo "<td class=\"rep\">Replication</td>";
            if ($uloc == "1") echo "<td class=\"loc\">Locks</td>";
            if ($u500 == "1") echo "<td class=\"500\">500s</td>";
            if ($uel == "1")  echo "<td class=\"el\">Elastic</td>";
            if ($umon == "1") echo "<td class=\"mon\">Mongo</td>";
            if ($ured == "1") echo "<td class=\"red\">Redis</td>";
            ?>
        </tr>
<?php
    if (ob_get_level() == 0) ob_start();
    echo str_repeat(' ',1024*128);
    flush();
    ob_flush();
    $result = $dbconnection->query("SELECT `st`.`servername`, `st`.`ip`, `st`.`db`, `st`.`mysql`, `st`.`err`, `st`.`el`, `st`.`mongo`, `st`.`redis` 
                                    FROM `user_roles` AS `ur` 
                                    JOIN `stats` AS `st` 
                                    ON `st`.`role` = `ur`.`role_id` 
                                    WHERE `ur`.`user_id` = {$uid}
                                    ORDER BY `st`.`servername`;") or die($dbconnection->error());
    while($array = $result->fetch_assoc()) {
        $serverip = $array["ip"];
        $server = $array["servername"];
        $errs = $array["err"];
        $elastic = $array["el"];
        $mongo = $array["mongo"];
        $redis = $array["redis"];
        $db = $array["db"];
        $mysql = $array["mysql"];
        $serverdb = $server . "_db";
		echo "<tr>";
        echo "<td id='" .$server. "_name'>" .$server. "</td>";
		if ($ula == "1") echo "<td class=\"la\" id='" .$server. "_la'></td>";
		if ($urep == "1") echo "<td id='" .$server. "_rep'></td>";
        if ($uloc == "1") echo "<td id='" .$server. "_locks'></td>";
		if ($u500 == "1") echo "<td id='" .$server. "_500'></td>";
		if ($uel == "1") echo "<td id='" .$server. "_elastic'></td>";
        if ($umon == "1") echo "<td id='" .$server. "_mongo'></td>";
        if ($ured == "1") echo "<td id='" .$server. "_redis'></td>";
    }

	$dbconnection->close();
?>
</table>