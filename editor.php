<div class="left_button" style="top: 20px;">
	<a href="http://<?php echo $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) ?>"><img src="images/back.png"></a>
</div>

<table class="main_table"><col span="5">
	<tr class="title">
        <td>Server</td>
        <td>IP Address</td>
        <td>Replication</td>
        <td>500s</td>
        <td>Elastic</td>
        <td>Locks</td>
    </tr>

<?php

$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");
$result = $dbconnection->query("SELECT `id`, `servername`, `ip`, `db`, `mysql`, `err`, `el` FROM `mymon`.`stats`;") or die($dbconnection->error);
$serverinfo = array();

while ($row_user = $result->fetch_assoc()) {
	echo "<tr>";
	echo "<td><input id=\"".trim($row_user['servername'])."^servername\" type=\"text\" value=\"".trim($row_user['servername'])."\" onchange=\"javascript: editor(this.id, this.value); \"></td>";
	echo "<td><input id=\"".trim($row_user['servername'])."^ip\" type=\"text\" value=\"".trim($row_user['ip'])."\" onchange=\"javascript: editor(this.id, this.value); \"></td>";
	echo "<td><input id=\"".trim($row_user['servername'])."^db\" type=\"checkbox\" ";
	if (trim($row_user['db']) == 1) echo "checked "; 
	echo "onchange=\"javascript: editor(this.id, this.checked); \"></td>";
	echo "<td><input id=\"".trim($row_user['servername'])."^err\" type=\"checkbox\" ";
	if (trim($row_user['err']) == 1) echo "checked ";
	echo "onchange=\"javascript: editor(this.id, this.checked); \"></td>";
	echo "<td><input id=\"".trim($row_user['servername'])."^el\" type=\"checkbox\" ";
	if (trim($row_user['el']) == 1) echo "checked ";
	echo "onchange=\"javascript: editor(this.id, this.checked); \"></td>";
	echo "<td><input id=\"".trim($row_user['servername'])."^mysql\" type=\"checkbox\" ";
	if (trim($row_user['mysql']) == 1) echo "checked ";
	echo "onchange=\"javascript: editor(this.id, this.checked); \"></td>";
	echo "</tr>";
}
echo ("</table>");
?>