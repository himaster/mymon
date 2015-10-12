<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if(isset($_GET['serverip'])){
    $serverip = $_GET['serverip'];
} else {
    die("serverip not set");
}

?>
<html>
<head>
    <title><?php echo $serverip ?> LA</title>
    <meta http-equiv="Content-Type" content="text/html; Charset=UTF-8">
    <script type="text/javascript" src="jquery.js"></script>
    <link rel="icon" href="http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']); ?>/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']); ?>/favicon.ico" type="image/x-icon">
</head>
<body bgcolor="black" align="center" text="white">
    <img id="graph" src="" />

    <script>
	function show_graph(){
            $.ajax({
        	url: "servergraph.php?serverip=<?php echo $serverip;?>&startdate=" + document.getElementById('startd').value,
                cache: false,
                success: function(html){
            		var d = new Date();
            		document.getElementById('graph').src = "images/graph.png?id=" + d.getMilliseconds();
//			$("#graph").html('');
                },
                error: function(){
			$("#graph").html("error!");
		}
            });
        }

        $(document).ready(function(){
            show_graph();
            setInterval('show_graph()',10000);
        });
    </script>
    <div style="position: fixed; z-index: 9999; width: 30px; height: 200px; overflow: hidden; left: 0px; top: 20px;">
        <a href="#" onclick="self.close()"><img src="./images/back.png"></a>
    </div>
    <form method="get" action="http://" .$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']). "/testgraph.php">
	<input type="hidden" name="serverip" value="<?php echo $serverip ?>">
	Date and time:
	<input type="datetime-local" name="startdate" id="startd" min="1979-12-31" value="01.06-2015 10:00">
    </form>
    <script>
	var nowd = new Date();
	nowd.setHours(nowd.getHours()+3);
	$("#startd").val(nowd.toJSON().slice(0,19));
    </script>
</body>
</html>
