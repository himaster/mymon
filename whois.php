
<a href="<?php echo $hostname ?>">
    <div class="left_button" id="back_button">
        <img src="images/back.png">
    </div>
</a>
<?php
exec("whois ".$_GET['ip'], $output);
echo "<div class='whois'>";
foreach ($output as $row) {
    echo $row."<br>";
}
echo "</div>";
