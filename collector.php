<?php

/**
 * Collector File Doc Comment
 *
 * @category Data_Collector
 * @package  MyMon
 * @author   himaster <himaster@mailer.ag>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://mymon.pkwteile.de
 */


require_once 'config.php';
require_once 'functions.php';
declare(ticks=1);
set_error_handler('errHandler');
pcntl_signal(SIGTERM, 'sigHandler');

$hostname   = 'https://mymon.pkwteile.de/';

$dbconnection = new mysqli($dbhost, $dbusername, $dbpass, $database);
if ($dbconnection->connect_errno) {
    printf("MySQL connection error: %s\n", $dbconnection->connect_error);
    exit();
}
$result = $dbconnection->query("SELECT ip, servername, db, mysql, err, el, mon, red, git FROM $database.`stats`;");
$dbconnection->close();

$parent = true;

while ($array = $result->fetch_assoc()) {
    $pid = pcntl_fork();
    if ($pid == -1) {
        die("Child process can't be created");
    } elseif ($pid) {
    } else {
        child_();
        exit;
    }
}
$result->free();
$pid = pcntl_fork();
if ($pid == -1) {
    die("Child process can't be created");
} elseif ($pid) {
} else {
    botips_();
    exit;
}
exit;

function botips_() {
    global $balancerip;
    global $ssh_callbacks;
    global $retry_num;
    global $docroot;
    global $loglevel;
    global $dbhost;
    global $dbusername;
    global $dbpass;
    global $database;

    $i = 1;

    if ($loglevel > 1) {
        common_log("botips_ - started.");
    }
    start:
    if (( ! $balancer_ssh_conn = ssh2_connect($balancerip, 22, $ssh_callbacks))
        or ( ! ssh2_auth_pubkey_file($balancer_ssh_conn, 'root', $docroot.'/id_rsa.pub', $docroot.'/id_rsa', ''))) {
        common_log("botips_ - retry #".$i++.".");
        sleep(1);
        if ($i < $retry_num) {
            goto start;
        } else {
            exit(1);
        }
    }
    $dbconnection = new mysqli($dbhost, $dbusername, $dbpass, $database);
    if ($dbconnection->connect_errno) {
        printf("MySQL connection error: %s\n", $dbconnection->connect_error);
        exit();
    }
    $i = 31;
    foreach (botips($balancer_ssh_conn) as $value) {
        $query = "INSERT INTO $database.`botips` (id, amount, ipaddr)
                  VALUES (".--$i.", ".$value['amount'].", '".$value['ipaddr']."')
                  ON DUPLICATE KEY UPDATE `amount` = ".$value['amount'].", `ipaddr` = '".$value['ipaddr']."';";
        $result = $dbconnection->query($query);
        if (!isset($result)) {
            common_log("botips_ - not updated!");
        }
    }
    unset($balancer_ssh_conn);
    if ($loglevel > 1) {
        common_log("botips_ - ended.");
    }
}

function child_() {
    global $array;
    global $stop_server;
    global $docroot;
    global $loglevel;
    global $ssh_callbacks;
    global $retry_num;
    global $dbhost;
    global $dbusername;
    global $dbpass;
    global $database;

    $serverip    = $array["ip"];
    $servername  = $array["servername"];
    $errs        = $array["err"];
    $elastic     = $array["el"];
    $db          = $array["db"];
    $mysql       = $array["mysql"];
    $mon         = $array["mon"];
    $red         = $array["red"];
    $git         = $array["git"];
    $i           = 1;

    if ($loglevel > 1) {
        common_log($servername. " - started.");
    }

    start:
    if (( ! $server_ssh_conn = ssh2_connect($serverip, 22, $ssh_callbacks))
        or ( ! ssh2_auth_pubkey_file($server_ssh_conn, 'root', $docroot.'/id_rsa.pub', $docroot.'/id_rsa', ''))) {
        common_log($servername." - retry #".$i++.".");
        sleep(1);
        if ($i < $retry_num) {
            goto start;
        } else {
            exit(1);
        }
    }
    $dbconnection = new mysqli($dbhost, $dbusername, $dbpass, $database);
    if ($dbconnection->connect_errno) {
        printf("MySQL connection error: %s\n", $dbconnection->connect_error);
        exit();
    }

    $value = la($server_ssh_conn, $serverip, $servername);
    $query = "UPDATE `$database`.`stats`
              SET `la`='".$value."', `timestamp`=CURRENT_TIMESTAMP
              WHERE `ip`='" .$serverip. "';";
    if ( ! $result = $dbconnection->query($query)) {
        common_log($servername." - LA not updated!");
    }

    $value = ($db == 1)?$dbconnection->escape_string(rep($server_ssh_conn, $serverip, $servername)):"";
    $query = "UPDATE `$database`.`stats`
              SET `rep`='".$value."', `timestamp`=CURRENT_TIMESTAMP
              WHERE `ip`='" .$serverip. "';";
    if ($loglevel > 1) {
        common_log($servername." - ".$query);
    }
    if ( ! $result = $dbconnection->query($query)) {
        common_log($servername." - REP not updated!");
    }

    $value = ($errs == 1)?err500($server_ssh_conn, $serverip, $servername):"";
    $query = "UPDATE `$database`.`stats`
              SET `500`='" .$value."', `timestamp`=CURRENT_TIMESTAMP
              WHERE `ip`='" .$serverip. "';";
    if ( ! $result = $dbconnection->query($query)) {
        common_log($servername." - 500 not updated!");
    }

    $value = ($elastic == 1)?elastic($server_ssh_conn, $serverip, $servername):"";
    $query = "UPDATE `$database`.`stats`
              SET `elastic`='" .$value."', `timestamp`=CURRENT_TIMESTAMP
              WHERE `ip`='" .$serverip. "';";
    if ( ! $result = $dbconnection->query($query)) {
        common_log($servername." - ELASTIC not updated!");
    }

    $value = ($mysql == 1)?locks($server_ssh_conn, $serverip, $servername):"";
    $query = "UPDATE `$database`.`stats`
              SET `locks`='" .$value."', `timestamp`=CURRENT_TIMESTAMP
              WHERE `ip`='" .$serverip. "';";
    if ( ! $result = $dbconnection->query($query)) {
        common_log($servername." - LOCKS not updated!");
    }

    $value = ($mon == 1)?mongo($server_ssh_conn, $serverip, $servername):"";
    $query = "UPDATE `$database`.`stats`
              SET `mongo`='" .$value."', `timestamp`=CURRENT_TIMESTAMP
              WHERE `ip`='" .$serverip. "';";
    if ( ! $result = $dbconnection->query($query)) {
        common_log($servername." - MONGO not updated!");
    }

    $value = ($red == 1)?redis($server_ssh_conn, $serverip, $servername):"";
    $query = "UPDATE `$database`.`stats`
              SET `redis`='" .$value."' , `timestamp`=CURRENT_TIMESTAMP
              WHERE `ip`='" .$serverip. "';";
    if ( ! $result = $dbconnection->query($query)) {
        common_log($servername." - REDIS not updated!");
    }

    $value = ($git == 1)?repo($server_ssh_conn, $serverip, "prod")."' , `test_repo`='" .repo($server_ssh_conn, $serverip, "dev"):"' , `test_repo`='";
    $query = "UPDATE `$database`.`stats`
              SET `master_repo`='" .$value."' , `timestamp`=CURRENT_TIMESTAMP
              WHERE `ip`='" .$serverip. "';";
    if ( ! $result = $dbconnection->query($query)) {
        common_log($servername." - git not updated!");
    }

    $dbconnection->close();
    unset($server_ssh_conn);

    if ($loglevel > 1) {
        common_log($servername. " - ended.");
    }
}

function la($ssh_conn, $serverip, $servername = null) {
    $la_string = substr(strrchr(ssh2_return($ssh_conn, "/usr/bin/uptime"), ":"), 1);
    $la = floatval(array_map("trim", explode(",", $la_string))[0]);
    $core = intval(ssh2_return($ssh_conn, "grep -c processor /proc/cpuinfo"));
    $percent = intval($la/$core*100);
    if ($percent < 75) {
        $fontcolor = "<span style=\"color: green;\">";
    } elseif (($percent >= 75) && ($percent < 100)) {
        $fontcolor = "<span style=\"color: #CAC003\">";
    } else {
        $fontcolor = "<span style=\"color: red\">";
    }

    return "<a title=\"".$la_string."\"
               href=\"/index.php?task=top&serverip=" .$serverip. "\"
               target=\"_blank\">" .$fontcolor. "<b>" .$percent. "%</b></span>\n</a>";
}

function rep($ssh_conn, $serverip, $servername = null) {
    global $loglevel;
    $data = array();
    $str = ssh2_return($ssh_conn, "printf %s \"$(mysql -e 'show slave status\G' | awk 'FNR>1')\"");
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

    return "<a title=\"".(array_key_exists("Last_SQL_Error", $data) ? $data["Last_SQL_Error"]:'').(array_key_exists("Last_IO_Error", $data) ? $data["Last_IO_Error"]:'')."\"
               href=\"#\"". $onclick . " >
               SQL: " .$sqlfontcolor. "<b>" .$sql. "</b></font>
               IO: " .$iofontcolor. "<b>" .$io. "</b></font>
               &#916;: " .$deltafontcolor. "<b>" .$delta. "</b></font>\n</a>";
}

function err500($ssh_conn, $serverip, $servername = null) {
    global $hostname;
    $str = trim(ssh2_return($ssh_conn, "cat /var/log/500err.log"));

    return '<a title="Click to show 500 errors" href="'.$hostname.'index.php?task=500err&serverip='.$serverip.'" target="_self">'.$str.'</a>';
}

function elastic($ssh_conn, $serverip, $servername = null) {
    $str = ssh2_return($ssh_conn, "date1=\$((\$(date +'%s%N') / 1000000));
                                     hostname=\$(ip -f inet addr show `[ \"\$HOSTNAME\" == \"front5.pkwteile.de\" ] && echo \"em2\" || echo \"eth1\"` | grep -Po 'inet \K[\d.]+')
                                     curl -sS -o /dev/null -XGET http://\$hostname:9200/_cluster/health?pretty;
                                     date2=\$((\$(date +'%s%N') / 1000000));
                                     echo -n \$((\$date2-\$date1));");
    if ($str == "Timeout") {
        $fontcolor = "<script type=\"text/javascript\">notify(\"".$servername.": elastic problem\");</script>
                      <font color=\"red\">";
    } else {
        $fontcolor = "<font color=\"green\">";
    }

    return $fontcolor.$str. " ms</font>";
}

function locks($ssh_conn, $serverip, $servername = null) {
    $query  = "SELECT info FROM INFORMATION_SCHEMA.PROCESSLIST WHERE state LIKE '%lock%' AND time > 30";
    $locked = trim(ssh2_return($ssh_conn, "mysql -Ne \"".$query."\" | wc -l"));
    $query  = "SHOW STATUS WHERE variable_name = 'Threads_connected'";
    $conns  = trim(ssh2_return($ssh_conn, "mysql -Nse \"".$query."\" | awk '{print $2}'"));
    if ($locked === "Timeout") {
        $locked = "T";
    }
    if ($conns === "Timeout") {
        $conns = "T";
    }
    if (($locked == "0") and ($conns < "5000")) {
        $fontcolor = "<font color=\"green\">";
    } else {
        slackbot($servername.": DB locks");
        $fontcolor = "<script type=\"text/javascript\">notify(\"".$servername.": DB locks\");</script>
                      <font color=\"red\">";
    }

    return $fontcolor.$conns. " / " .$locked. "</font>";
}

function mongo($ssh_conn, $serverip, $servername = null) {
    $str = ssh2_return($ssh_conn, "date1=\$((\$(date +'%s%N') / 1000000));
                mongo admin --quiet --eval 'printjson(db.serverStatus().connections.current)' 1>/dev/null;
                date2=\$((\$(date +'%s%N') / 1000000));
                echo -n \$((\$date2-\$date1));");
    if ($str == "Timeout") {
        slackbot($servername.": mongo problem");
        $fontcolor = "<script type=\"text/javascript\">notify(\"".$servername.": mongo problem\");</script>
                      <font color=\"red\">";
    } else {
        $fontcolor = "<font color=\"green\">";
    }

    return $fontcolor.$str. " ms</font>";
}

function redis($ssh_conn, $serverip, $servername = null) {
    $str = ssh2_return($ssh_conn, "date1=\$((\$(date +'%s%N') / 1000000));
                                     redis-cli info 1>/dev/null;
                                     date2=\$((\$(date +'%s%N') / 1000000));
                                     echo -n \$((\$date2-\$date1));");
    if ($str == "Timeout") {
        slackbot($servername.": redis problem");
        $fontcolor = "<script type=\"text/javascript\">notify(\"".$servername.": redis problem\");</script>
                      <font color=\"red\">";
    } else {
        $fontcolor = "<font color=\"green\">";
    }

    return $fontcolor.$str. " ms</font>";
}

function repo($ssh_conn, $serverip, $repository) {
    $str = ssh2_return($ssh_conn, "cd /home/developer/www/fuel.$repository/ && git rev-parse HEAD");
    #if ($str == "Timeout") {
    #    slackbot($servername.": redis problem");
    #    $fontcolor = "<script type=\"text/javascript\">notify(\"".$servername.": redis problem\");</script>
    #                  <font color=\"red\">";
    #} else {
    #    $fontcolor = "<font color=\"green\">";
    #}

    return $str;
}

function botips($ssh_conn) {
    global $iplistnum;
    $str = ssh2_return($ssh_conn, "tail -n 1000000 /var/log/nginx/access.log |
                                    awk '{print $1}' |
                                    sort |
                                    uniq -c |
                                    sort -n |
                                    tail -n".$iplistnum);
    $i = 0;
    foreach (explode("\n", rtrim($str, "\n")) as $cLine) {
        $i++;
        $cLine = trim($cLine);
        list($ipaddrarray[$i]['amount'], $ipaddrarray[$i]['ipaddr']) = explode(' ', "$cLine ");
    }

    return $ipaddrarray;
}
