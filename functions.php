<?php
function no_injection($str='') { 
    $str = stripslashes($str); 
    $str = mysql_real_escape_string($str); 
    $str = trim($str); 
    $str = htmlspecialchars($str); 
    return $str; 
} 
?>