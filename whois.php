<?php
include "backbutton.php";

exec("whois ".$_GET['ip'], $output);
echo "<div class='whois'>";
foreach ($output as $row) {
    echo $row."<br>";
}
echo "</div>";
