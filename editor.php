<?php

// configuration
$url = 'http://netbox.co/mymon/';
$file = '/var/www/netbox.co/mymon/servers.conf';
$mass = file($file);

// check if form has been submitted
if (isset($_POST['text']))
{
    // save the text contents
    file_put_contents($file, $_POST['text']);

    // redirect to form again
    header("Location: ".$url);
    printf('<a href="%s">Moved</a>.', htmlspecialchars($url));
    exit();
}

// read the textfile
$text = file_get_contents($file);
?>
<html>
<head>
    <link rel="icon" href="http://netbox.co/mymon/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="http://netbox.co/mymon/favicon.ico" type="image/x-icon">
</head>

<body bgcolor="black" text="white" >
<div style="position: fixed; z-index: 9999; width: 30px; height: 200px; overflow: hidden; left: 0px; top: 20px;">
	<a href="http://netbox.co/mymon/"><img src="./images/back.png"></a>
</div>
<span align="center">
<!-- HTML form -->
<h2>Server list</h2>
<h4>IP&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspname&nbspweb&nbspDB</h4>
<form action="" method="post">
<textarea name="text" cols="30" rows="<?php echo count($mass)?>" style="background-color: lightgray"><?php echo htmlspecialchars($text) ?></textarea><p>
<input type="submit" value="Сохранить" onClick='window.location.href="http://netbox.co/mymon/"' />
<input type="reset" />
</form>
</span>
</body>
</html>