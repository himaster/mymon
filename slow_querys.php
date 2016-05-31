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
            $query = '';
            $a = explode(' ', $row);
            $time = trim($a[3].$a[4]);
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
foreach (multi_array_unique($data, 'query') as &$value) {
    echo "<div class=\"dropdown centerinner\">";
    echo "<button class=\"btn dropdown-toggle buttons\" type=\"button\" id=\"dropdownButton".$value['id']."\" data-toggle=\"dropdown\">";
    echo    $value['query'];
    echo    "<span class=\"caret\"></span>";
    echo "</button>";
    echo "<ul class=\"dropdown-menu\" role=\"menu\" aria-labelledby=\"dropdownButton".$value['id']."\">";
    echo "<li class=\"menuitem\" role=\"presentation\">DB: ".$value['database']."</li>";
    echo "<li class=\"menuitem\" role=\"presentation\">Time: ".$value['time']."</li>";
    echo "<li class=\"menuitem\" role=\"presentation\">Host: ".$value['host']."</li>";
    echo "<li class=\"menuitem\" role=\"presentation\">Query time: ".$value['query_time']."</li>";
    echo "<li class=\"menuitem\" role=\"presentation\">Lock time: ".$value['lock_time']."</li>";
    echo "<li class=\"menuitem\" role=\"presentation\">Rows examined: ".$value['rows_examined']."</li>";
    echo "<li class=\"menuitem\" role=\"presentation\">Rows affected: ".$value['rows_affected']."</li>";
    echo "</ul></div>";
}


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
