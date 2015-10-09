<?php
	$file = fopen("./servers.conf", "r");
	if (ob_get_level() == 0) ob_start();
        echo str_repeat(' ',1024*128);
?>
<html>
<head>
	<title>MyMon</title>
	<meta http-equiv="Content-Type" content="text/html; Charset=UTF-8">
	<script type="text/javascript">
	<!--
	function toggle_visibility(id) {
		var e = document.getElementById(id);
		if(e.style.display == 'block')
			e.style.display = 'none';
		else
			e.style.display = 'block';
		}
	//-->
	</script>
	<script type="text/javascript" src="jquery.js"></script>
	<link rel="icon" href="http://netbox.co/mymon/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="http://netbox.co/mymon/favicon.ico" type="image/x-icon">
</head>
<body bgcolor="black">
	<div style="position: fixed; z-index: 9999; width: 30px; height: 200px; overflow: hidden; left: 0px; top: 20px;">
		<a href="editor.php"><img src="./images/button.png"></a>
	</div>
	<div style="position: fixed; z-index: 9999; width: 30px; height: 200px; overflow: hidden; left: 0px; top: 50px;">
		<a href="#" onclick="toggle_visibility('my_div')"><img src="./images/profile.png"></a>
	</div>
	<div id="my_div" style="position: fixed; width: 200px; height: 200px; left: 0px; top: 70px; display: none;">
		<img src="./images/menu.png">
		<span style="position: fixed; left: 20px; top: 80px; color: black;">
		<b>Profile</b><p>
		<?php if (isset($_SERVER['PHP_AUTH_USER'])) {
			echo $_SERVER['PHP_AUTH_USER'];
			echo "<p style='font-size: 11;'>IP: ".$_SERVER['REMOTE_ADDR']."</p>";
			echo "<p><a href='http://logout@".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."'>logout</a>";
		} else {
			echo "Local user";
			echo "<p style='font-size: 11;'>IP: ".$_SERVER['REMOTE_ADDR']."</p>";
		}
		?>
		</span>
	</div>

	<table border="2" align="center" bgcolor="lightgray">
            <col span="4">
                <tr>
                        <td width="80"><b>Server</b></td>
                        <td width="120"><b>Load Averages</b></td>
                        <td width="160"><b>Replication</b></td>
                        <td width="60"><b>500s</b></td>
                        <td width="60"><b>Elastic</b></td>
                </tr>
		
<?php
        flush();
        ob_flush();
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
                echo "<td><b><a href='http://netbox.co/mymon/testgraph.php?serverip=" .$serverip. "' target='_blank' style='text-decoration: none;'><font color='black'>" .$server. "</font></b></td>";
				echo "<td><div id='" .$server. "_la'></div></td>";
				echo "<td><div id='" .$server. "_rep'></div></td>";
				echo "<td><div id='" .$server. "_500'></div></td>";
				echo "<td><div id='" .$server. "_elastic'></div></td>";
?>
	<script>
	function myAjax(serverip){
	    $('#test_div').html('processing...');
	    document.getElementById('test_div').style.display = 'block';
	    $.ajax({
		url: 'replica.php?serverip=' + serverip,
		success: function(){
			$('#test_div').html('success');
			setTimeout("document.getElementById('test_div').style.display = 'none'", 5000);
		},
		error: function(){
			$('#test_div').html('error');
			setTimeout("document.getElementById('test_div').style.display = 'none'", 5000);
		}
	    });
	};
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
<font color="white"><div id="test_div" style="display: none; position:fixed; width:100%; background-color: #565051; height:20px; bottom:20px; left: 0px; "></div></font>
</body>
</html>
