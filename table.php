<?php 
    include "menu.php";
?>
<table class="main_table" id="main_table">
    <col span="5">
        <tr class="title">
            <td>Server</td>
            <?php 
            if ($ula == "1")  echo "<td class=\"la\">Load Averages<div class=\"colons\" id=\"col1\">1*</div></td>";
            if ($urep == "1") echo "<td class=\"rep\">Replication<div class=\"colons\" id=\"col2\">2*</div></td>";
            if ($uloc == "1") echo "<td class=\"loc\">Locks<div class=\"colons\" id=\"col3\">3*</div></td>";
            if ($u500 == "1") echo "<td class=\"500\">500s<div class=\"colons\" id=\"col4\">4*</div></td>";
            if ($uel == "1")  echo "<td class=\"el\">Elastic<div class=\"colons\" id=\"col5\">5*</div></td>";
            if ($umon == "1") echo "<td class=\"mon\">Mongo<div class=\"colons\" id=\"col6\">6*</div></td>";
            if ($ured == "1") echo "<td class=\"red\">Redis<div class=\"colons\" id=\"col7\">7*</div></td>";
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