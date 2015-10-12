<?php 
    include "menu.php";
    include "table_header.html";

    if (ob_get_level() == 0) ob_start();
    echo str_repeat(' ',1024*128);
    flush();
    ob_flush();
    $file = fopen("./servers.conf", "r");
    while(! feof($file)) {
        $line = fgets($file);
        if ($line[0] == '#') {
			continue;
		}
        $array = explode(" ", $line);
        $serverip = $array[0];
        $server = $array[1];
        $errs = $array[2];
        $elastic = $array[3];
        $db = $array[4];
        $serverdb = $server . "_db";
		echo "<tr>";
        echo "<td style='text-align: left'><b><a href='http://netbox.co/mymon/testgraph.php?serverip=" .$serverip. "' target='_blank' style='text-decoration: none;'><font color='black'>" .$server. "</font></b></td>";
		echo "<td><div id='" .$server. "_la'></div></td>";
		echo "<td><div id='" .$server. "_rep'></div></td>";
		echo "<td><div id='" .$server. "_500'></div></td>";
		echo "<td><div id='" .$server. "_elastic'></div></td>";
        #include "ajax.php";
?>
        
<script>
function show_<?php echo $server; ?>(){
        $.ajax({
            url: "server.php?serverip=<?php echo $serverip;?>&task=la",
            cache: false,
            success: function(html){
                $("#<?php echo $server. '_la'; ?>").html(html);
            },
            error: function(){
                $.ajax(this);
            }
        });
    <?php if (isset($db)) { ?>
        $.ajax({
            url: "server.php?serverip=<?php echo $serverip;?>&task=rep",
            cache: false,
            success: function(html){
                $("#<?php echo $server. '_rep'; ?>").html(html);
            },
            error: function(){
                $.ajax(this);
            }
        });
    <?php } ?>
    <?php if ($errs == 1) { ?>
        $.ajax({
            url: "server.php?serverip=<?php echo $serverip;?>&task=500",
            cache: false,
            success: function(html){
                    $("#<?php echo $server. '_500'; ?>").html(html);
            },
            error: function(){
                    $.ajax(this);
            }
        });
    <?php } ?>
    <?php if ($elastic == 1) { ?>
        $.ajax({
            url: "server.php?serverip=<?php echo $serverip;?>&task=elastic",
            cache: false,
            success: function(html){
                    $("#<?php echo $server. '_elastic'; ?>").html(html);
            },
            error: function(){
                    $.ajax(this);
            }
        });
    <?php } ?>
    }

    $(document).ready(function(){
        show_<?php echo $server; ?>();
        setInterval('show_<?php echo $server; ?>()',10000);
    });
</script>
<?php
    }
	fclose($file);
?>
</table>