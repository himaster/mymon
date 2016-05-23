<?php

require_once "config.php";
require_once "functions.php";

$slackbotlevel = 'full';

$serverip = '217.118.19.156';
$servername = 'backend5';
$mysql_conname = "mysql_".$servername;

$$mysql_conname = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon")
                  or die($$mysql_conname->connect_errno."\n");
$ssh_conname = "ssh_".$servername;
if (( ! $$ssh_conname = ssh2_connect($serverip, 22, $ssh_callbacks))
        or ( ! ssh2_auth_pubkey_file($$ssh_conname, 'root', $docroot.'/id_rsa.pub', $docroot.'/id_rsa', ''))) {
        die("SSH connect not opened!");
}

$query = "UPDATE `mymon`.`stats` SET `rep`='".rep($$ssh_conname, $serverip, $servername).
                "' , `timestamp`=CURRENT_TIMESTAMP WHERE `ip`='" .$serverip. "';";

echo $query;

function rep($connection, $serverip, $servername = null)
{
    global $loglevel;

    $data = array();
    $str = ssh2_return($connection, "printf %s \"$(mysql -e 'show slave status\G' | awk 'FNR>1')\"");
    foreach (explode("\n", $str) as $cLine) {
        if (strpos($cLine, "Timeout") != false) {
            return "<font color=\"red\">".strpos($cLine, "Timeout")." - stopped</font>";
        }
        list($cKey, $cValue) = explode(':', "$cLine:");
        $data[trim($cKey)] = trim($cValue);
    }
    $onclick = "";
    if ($loglevel > 0) {
        common_log($servername.' - SQL :'.$data["Slave_SQL_Running"].' IO :'.$data["Slave_IO_Running"]);
    }
    if (array_key_exists("Slave_SQL_Running", $data) && ($data["Slave_SQL_Running"] == "Yes")) {
        $sqlfontcolor = "<font color=\"green\">";
        $sql = "&#10003;";
    } else {
        slackbot($servername.": replication SQL problem");
        $sqlfontcolor = "<script type=\"text/javascript\">notify(\"$servername: replication SQL problem\");</script>".
                        "<font color=\"red\">";
        $sql = "x";
        $onclick = "onclick=\"javascript:   if(event.ctrlKey || event.metaKey) {
                                                if(confirm('Want to RESTART replication?')) {
                                                    replica_restart('" .$serverip. "');
                                                }
                                            }
                                            else {
                                                if(confirm('Want to skip one error and start?')) {
                                                    replica_repair('" .$serverip. "');
                                                }
                                            }
                                            return false;\"";
    }
    if (array_key_exists("Slave_IO_Running", $data) && ($data["Slave_IO_Running"] == "Yes")) {
        $iofontcolor = "<font color=\"green\">";
        $io = "&#10003;";
    } else {
        slackbot($servername.": replication IO problem");
        $iofontcolor =  "<script type=\"text/javascript\">notify(\"$servername: replication IO problem\");</script>".
                        "<font color=\"red\">";
        $io = "x";
        $onclick = "onclick=\"javascript:   if(event.ctrlKey || event.metaKey) {
                                                if(confirm(\'Want to RESTART replication?\')) {
                                                    replica_restart(\'" .$serverip. "\');
                                                }
                                            }
                                            else {
                                                if(confirm(\'Want to skip one error and start?\')) {
                                                    replica_repair(\'" .$serverip. "\');
                                                }
                                            }
                                            return false;\"";
    }
    if (array_key_exists("Seconds_Behind_Master", $data) && ($data["Seconds_Behind_Master"] == "0")) {
        $deltafontcolor = "<font color=\"green\">";
        $delta = "0";
    } elseif ((array_key_exists("Seconds_Behind_Master", $data) && ($data["Seconds_Behind_Master"] == "NULL"))
              || ! array_key_exists("Seconds_Behind_Master", $data)) {
        $deltafontcolor = "<font color=\"red\">";
        $delta = "x";
    } else {
        $deltafontcolor = "<font color=\"red\">";
        $delta = $data["Seconds_Behind_Master"];
    }

    return "<a title=\"".(array_key_exists("Last_SQL_Error", $data) ? htmlspecialchars($data["Last_SQL_Error"]):'')."\" 
               href=\"#\"". $onclick . " >
               SQL: " .$sqlfontcolor. "<b>" .$sql. "</b></font> 
               IO: " .$iofontcolor. "<b>" .$io. "</b></font> 
               &#916;: " .$deltafontcolor. "<b>" .$delta. "</b></font>\n</a>";
}
