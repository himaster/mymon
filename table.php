<?php

if ($_SERVER["SCRIPT_NAME"] != "/index.php") {
    die();
}
?>

<div id="load_fade" class="load_fading">
    <img src="images/loading.gif">
</div>
<div id="logo">
    <img src="images/mymon.png">
</div>
<div class="loader" id="refresher">
</div>
<div class="loader" id="loader" style="display: none;">
    <img src="images/loader.png">
</div>
    <table class="main_table table-striped table-hover" id="main_table">
        <col span="5">
            <tr class="title">
                <th>Server</th>
                <?php
                if ($ula == "1") {
                    echo "<th class=\"la\">LA<div class=\"colons\" id=\"col1\">1</div></th>";
                }
                if ($urep == "1") {
                    echo "<th class=\"rep\">Replication<div class=\"colons\" id=\"col2\">2</div></th>";
                }
                if ($uloc == "1") {
                    echo "<th class=\"loc\">Locks<div class=\"colons\" id=\"col3\">3</div></th>";
                }
                if ($u500 == "1") {
                    echo "<th class=\"500\">500s<div class=\"colons\" id=\"col4\">4</div></th>";
                }
                if ($uel == "1") {
                    echo "<th class=\"el\">Elastic<div class=\"colons\" id=\"col5\">5</div></th>";
                }
                if ($umon == "1") {
                    echo "<th class=\"mon\">Mongo<div class=\"colons\" id=\"col6\">6</div></th>";
                }
                if ($ured == "1") {
                    echo "<th class=\"red\">Redis<div class=\"colons\" id=\"col7\">7</div></th>";
                }
                ?>
            </tr>
    <?php
    if (ob_get_level() == 0) {
        ob_start();
    }
        echo str_repeat(' ', 1024*128);
        flush();
        ob_flush();
        include "menu.php";
        include "messagebox.php";
        $result = $dbconnection->query("SELECT `st`.`servername`,
                                               `st`.`ip`,
                                               `st`.`db`,
                                               `st`.`mysql`,
                                               `st`.`err`,
                                               `st`.`el`,
                                               `st`.`mongo`,
                                               `st`.`redis`,
                                               `st`.`color`
                                        FROM $database.`user_roles` AS `ur`
                                        JOIN $database.`stats` AS `st`
                                        ON `st`.`role` = `ur`.`role_id`
                                        WHERE `ur`.`user_id` = {$uid}
                                        ORDER BY LEFT(`st`.`servername`,3),
                                                 CAST( SUBSTRING(`st`.`servername`, INSTR(`st`.`servername`,  '-' ) +1 ) AS UNSIGNED),
                                                 `st`.`servername`;") or die($dbconnection->error());

    while ($array = $result->fetch_assoc()) {
        $serverip = $array["ip"];
        $server = $array["servername"];
        $errs = $array["err"];
        $elastic = $array["el"];
        $mongo = $array["mongo"];
        $redis = $array["redis"];
        if ($array["color"] == "#ffffff") {
            $color = "";
        } else {
            $color = $array["color"];
        }
        $db = $array["db"];
        $mysql = $array["mysql"];
        $serverdb = $server . "_db";
        echo "<tr>";
        echo "<td style=\"background-color: ". $color ."; z-index: 0\" id='" .$server. "_name'>" .$server. "</td>";
        if ($ula == "1") {
            echo "<td class=\"la\" id='" .$server. "_la'></td>";
        }
        if ($urep == "1") {
            echo "<td class=\"rep\" id='" .$server. "_rep'></td>";
        }
        if ($uloc == "1") {
            echo "<td class=\"loc\" id='" .$server. "_locks'></td>";
        }
        if ($u500 == "1") {
            echo "<td class=\"500\" id='" .$server. "_500'></td>";
        }
        if ($uel == "1") {
            echo "<td class=\"el\" id='" .$server. "_elastic'></td>";
        }
        if ($umon == "1") {
            echo "<td class=\"mon\" id='" .$server. "_mongo'></td>";
        }
        if ($ured == "1") {
            echo "<td class=\"red\" id='" .$server. "_redis'></td>";
        }
    }

        $dbconnection->close();
    ?>
    </table>
</div>
<div id="status_div" class="status_bar"></div>
