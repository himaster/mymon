<?php

if ($_SERVER["SCRIPT_NAME"] != "/index.php") {
    die();
}

backButton("/index.php?task=botips");

exec("whois ".$_GET['ip'], $output);
echo "<div class='whois'>";
foreach ($output as $row) {
    if ((stripos($row, 'netname') !== false)
        or (stripos($row, 'descr') !== false)
        or (stripos($row, 'organization') !== false)
        or (stripos($row, 'orgname') !== false)
        or (stripos($row, 'orgtechname') !== false)
        or (stripos($row, 'netrange') !== false)
        or (stripos($row, 'CIDR') !== false)
        or (stripos($row, 'inetnum') !== false)
        or (stripos($row, 'route') !== false)) {
        echo "<b>".$row."</b><br>";
    } else {
        echo $row."<br>";
    }
}
echo "</div>";
