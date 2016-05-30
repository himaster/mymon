<?php
include "backbutton.php";

exec("whois ".$_GET['ip'], $output);
echo "<div class='whois'>";
foreach ($output as $row) {
    if ((stripos($row, 'netname') !== false)
        or (stripos($row, 'descr') !== false)
        or (stripos($row, 'organization') !== false)
        or (stripos($row, 'orgname') !== false)
        or (stripos($row, 'orgtechname') !== false)) {
        echo "<b>".$row."</b><br>";
    } else {
        echo $row."<br>";
    }
}
echo "</div>";
