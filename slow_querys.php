<?php
$fp = fopen('/var/log/mysql/slow.log', 'a+');
$query = '';
$id = 0;
$data = array(array());
//die("Begin");
if ($fp) {
    while (!feof($fp)) {
        $row = fgets($fp, 1024);
        if (stripos($row, '# Time:') !== false) {
            $data[] = array(  "id" => $id++,
                              "time" => $time,
                              "host" => $host,
                              "query_time" => $query_time,
                              "lock_time" => $lock_time,
                              "rows_examined" => $rows_examined,
                              "rows_affected" => $rows_affected,
                              "database" => $database,
                              "query" => $query);
            echo "<div id=\"dd$id\" class=\"wrapper-dropdown-4\">";
            echo $query;
            echo "<ul class=\"dropdown\">";
            echo "<li><input id=\"el-1\" type=\"checkbox\" name=\"el-1\" value=\"db\" /><label for=\"el-1\">DB: $database</label</li>";
            //echo "<li><label>Time: $time</label</li>";
            //echo "<li><label>Host: $time</label</li>";
            //echo "<li><label>Query time: $time</label</li>";
            //echo "<li><label>Lock time: $time</label</li>";
            //echo "<li><label>Rows examined: $rows_examined</label</li>";
            //echo "<li><label>Rows affected: $rows_affected</label</li>";
            echo "</ul></div>";
            $query = '';
            $a = explode(' ', $row);
            $time = trim($a[3]);
        } else if (stripos($row, '# User@Host:') !== false) {
            $a = explode(' ', $row);
            $host = $a[5];
        } else if (stripos($row, '# Schema:') !== false or
                   stripos($row, '# Bytes_sent') !== false or
                   stripos($row, 'SET') !== false) {
        } else if (stripos($row, '# Query_time:') !== false) {
            $a = explode(' ', $row);
            $query_time = $a[2];
            $lock_time = $a[5];
            $rows_examined = $a[11];
            $rows_affected = $a[14];
        } else if (stripos($row, 'USE') !== false) {
            $a = explode(' ', $row);
            $database = $a[1];
        } else {
            $query .= trim($row)." ";
        }
    }
} else {
    die("Can't open file");
}
$query = '';
fclose($fp);
die("Done");

function multi_array_unique($array, $key)
{
    $temp_array = array();
    foreach ($array as &$v) {
        if (!isset($temp_array[$v[$key]])) {
            $temp_array[$v[$key]] =& $v;
        }
    }
    $array = array_values($temp_array);
    return $array;
}
