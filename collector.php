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

dl("/var/lib/openshift/57f26d570c1e66cfba00019f/app-root/repo/ssh2-0.13/ssh2.so");


require_once 'config.php';
require_once 'functions.php';

$error_log = 'errors.txt';
$hostname  = 'https://mymon.pkwteile.de/';

declare(ticks=1);

set_error_handler('errHandler');

pcntl_signal(SIGTERM, 'sigHandler');

$connection = new mysqli($host, $username, $pass, $db) or die("Mysql error.".$dbconnection->connect_errno."\n");
$result = $connection->query("SELECT ip, servername, db, mysql, err, el, mon, red, git FROM $db.`stats`;")
        or die($connection->error);
$connection->close();

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

function botips_()
{
    global $balancerip;
    global $ssh_callbacks;
    global $retry_num;
    global $docroot;
    global $loglevel;

    $i = 1;

    if ($loglevel > 1) {
        common_log("botips_ - started.");
    }
    start:
    if (( ! $connection = ssh2_connect($balancerip, 22, $ssh_callbacks))
        or ( ! ssh2_auth_pubkey_file($connection, 'root', $docroot.'/id_rsa.pub', $docroot.'/id_rsa', ''))) {
        common_log("botips_ - retry #".$i++.".");
        sleep(1);
        if ($i < $retry_num) {
            goto start;
        } else {
            exit(1);
        }
    }
    $mysql_balancer = new mysqli($host, $username, $pass, $db) or die("Mysql error.".$dbconnection->connect_errno."\n");
    $i = 31;
    foreach (botips($connection) as $value) {
        $query = "INSERT INTO $db.`botips` (id, amount, ipaddr)
                  VALUES (".--$i.", ".$value['amount'].", '".$value['ipaddr']."')
                  ON DUPLICATE KEY UPDATE `amount` = ".$value['amount'].", `ipaddr` = '".$value['ipaddr']."';";
        $result = $mysql_balancer->query($query);
        if (!isset($result)) {
            common_log("botips_ - not updated!");
        }
    }
    if ($loglevel > 1) {
        common_log("botips_ - ended.");
    }
}

function child_()
{
    global $array;
    global $stop_server;
    global $docroot;
    global $loglevel;
    global $ssh_callbacks;
    global $retry_num;

    $serverip    = $array["ip"];
    $servername  = $array["servername"];
    $errs        = $array["err"];
    $elastic     = $array["el"];
    $db          = $array["db"];
    $mysql       = $array["mysql"];
    $mon         = $array["mon"];
    $red         = $array["red"];
    $git         = $array["git"];
    $ssh_conname = "ssh_".$servername;
    $i           = 1;

    if ($loglevel > 1) {
        common_log($servername. " - started.");
    }

    start:
    if (( ! $$ssh_conname = ssh2_connect($serverip, 22, $ssh_callbacks))
        or ( ! ssh2_auth_pubkey_file($$ssh_conname, 'root', $docroot.'/id_rsa.pub', $docroot.'/id_rsa', ''))) {
        common_log($servername." - retry #".$i++.".");
        sleep(1);
        if ($i < $retry_num) {
            goto start;
        } else {
            exit(1);
        }
    }
    $mysql_conname = "mysql_".$servername;
    $$mysql_conname = new mysqli($host, $username, $pass, $db) or die("Mysql error.".$dbconnection->connect_errno."\n");
    $query = "UPDATE $db.`stats` SET `la`='".la($$ssh_conname, $serverip, $servername).
            "' , `timestamp`=CURRENT_TIMESTAMP WHERE `ip`='" .$serverip. "';";
    $result = $$mysql_conname->query($query);
    if (!isset($result)) {
        common_log($servername." - LA not updated!");
    }
    unset($result);
    if ($db == 1) {
        $query = "UPDATE $db.`stats` SET `rep`='".$$mysql_conname->escape_string(rep($$ssh_conname, $serverip, $servername)).
                "' , `timestamp`=CURRENT_TIMESTAMP WHERE `ip`='" .$serverip. "';";
    } else {
        $query = "UPDATE $db.`stats` SET `rep`='' WHERE `ip`='" .$serverip. "';";
    }
    if ($loglevel > 1) {
        common_log($servername." - ".$query);
    }
    $result = $$mysql_conname->query($query);
    if (!isset($result)) {
        common_log($servername." - REP not updated!");
    }
    unset($result);
    if ($errs == 1) {
        $query = "UPDATE $db.`stats` SET `500`='" .err500($$ssh_conname, $serverip, $servername).
                "' , `timestamp`=CURRENT_TIMESTAMP WHERE `ip`='" .$serverip. "';";
    } else {
        $query = "UPDATE $db.`stats` SET `500`='' WHERE `ip`='" .$serverip. "';";
    }
    $result = $$mysql_conname->query($query);
    if (!isset($result)) {
        common_log($servername." - 500 not updated!");
    }
    unset($result);
    if ($elastic == 1) {
        $query = "UPDATE $db.`stats` SET `elastic`='" .elastic($$ssh_conname, $serverip, $servername).
                "' , `timestamp`=CURRENT_TIMESTAMP WHERE `ip`='" .$serverip. "';";
    } else {
        $query = "UPDATE $db.`stats` SET `elastic`='' WHERE `ip`='" .$serverip. "';";
    }
    $result = $$mysql_conname->query($query);
    if (!isset($result)) {
        common_log($servername." - ELASTIC not updated!");
    }
    unset($result);
    if ($mysql == 1) {
        $query = "UPDATE $db.`stats` SET `locks`='" .locks($$ssh_conname, $serverip, $servername).
                "' , `timestamp`=CURRENT_TIMESTAMP WHERE `ip`='" .$serverip. "';";
    } else {
        $query = "UPDATE $db.`stats` SET `locks`='' WHERE `ip`='" .$serverip. "';";
    }
    $result = $$mysql_conname->query($query);
    if (!isset($result)) {
        common_log($servername." - LOCKS not updated!");
    }
    unset($result);
    if ($mon == 1) {
        $query = "UPDATE $db.`stats` SET `mongo`='" .mongo($$ssh_conname, $serverip, $servername).
                "' , `timestamp`=CURRENT_TIMESTAMP WHERE `ip`='" .$serverip. "';";
    } else {
        $query = "UPDATE $db.`stats` SET `mongo`='' WHERE `ip`='" .$serverip. "';";
    }
    $result = $$mysql_conname->query($query);
    if (!isset($result)) {
        common_log($servername." - MONGO not updated!");
    }
    unset($result);
    if ($red == 1) {
        $query = "UPDATE $db.`stats` SET `redis`='" .redis($$ssh_conname, $serverip, $servername).
                "' , `timestamp`=CURRENT_TIMESTAMP WHERE `ip`='" .$serverip. "';";
    } else {
        $query = "UPDATE $db.`stats` SET `redis`='' WHERE `ip`='" .$serverip. "';";
    }
    $result = $$mysql_conname->query($query);
    if (!isset($result)) {
        common_log($servername." - REDIS not updated!");
    }
    unset($result);
    if ($git == 1) {
        $query = "UPDATE $db.`stats` SET
                     `master_repo`='" .repo($$ssh_conname, $serverip, "prod").
                "' , `test_repo`='" .repo($$ssh_conname, $serverip, "dev").
                "' , `timestamp`=CURRENT_TIMESTAMP WHERE `ip`='" .$serverip. "';";
    } else {
        $query = "UPDATE $db.`stats` SET `500`='' WHERE `ip`='" .$serverip. "';";
    }
    $result = $$mysql_conname->query($query);
    if (!isset($result)) {
        common_log($servername." - 500 not updated!");
    }
    $$mysql_conname->close();
    unset($$mysql_conname);
    unset($$ssh_conname);
    if ($loglevel > 1) {
        common_log($servername. " - ended.");
    }
}

function la($connection, $serverip, $servername = null)
{
    global $hostname;
    $la_string = substr(strrchr(ssh2_return($connection, "/usr/bin/uptime"), ":"), 1);
    $la = floatval(array_map("trim", explode(",", $la_string))[0]);
    $core = intval(ssh2_return($connection, "grep -c processor /proc/cpuinfo"));
    $percent = intval($la/$core*100);
    if ($percent < 75) {
        $fontcolor = "<span style=\"color: green;\">";
    } elseif (($percent >= 75) && ($percent < 100)) {
        $fontcolor = "<span style=\"color: #CAC003\">";
    } else {
        $fontcolor = "<span style=\"color: red\">";
    }

    return "<a title=\"".$la_string."\"
               href=\"".$hostname."index.php?task=top&serverip=" .$serverip. "\"
               target=\"_blank\">" .$fontcolor. "<b>" .$percent. "%</b></span>\n</a>";
}

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

function err500($connection, $serverip, $servername = null)
{
    global $hostname;
    $str = trim(ssh2_return($connection, "cat /var/log/500err.log"));

    return "<a title=\"Click to show 500 errors\"
             href=". $hostname. "index.php?task=500err&serverip=" .$serverip. "
             target=\"_self\">" .$str. "\n</a>";
}

function elastic($connection, $serverip, $servername = null)
{
    $str = ssh2_return($connection, "date1=\$((\$(date +'%s%N') / 1000000));
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

function locks($connection, $serverip, $servername = null)
{
    $query  = "SELECT info FROM INFORMATION_SCHEMA.PROCESSLIST WHERE state LIKE '%lock%' AND time > 30";
    $locked = trim(ssh2_return($connection, "mysql -Ne \"".$query."\" | wc -l"));
    $query  = "SHOW STATUS WHERE variable_name = 'Threads_connected'";
    $conns  = trim(ssh2_return($connection, "mysql -Nse \"".$query."\" | awk '{print $2}'"));
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

function mongo($connection, $serverip, $servername = null)
{
    $str = ssh2_return($connection, "date1=\$((\$(date +'%s%N') / 1000000));
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

function redis($connection, $serverip, $servername = null)
{
    $str = ssh2_return($connection, "date1=\$((\$(date +'%s%N') / 1000000));
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

function repo($connection, $serverip, $repository)
{
    $str = ssh2_return($connection, "cd /home/developer/www/fuel.$repository/ && git rev-parse HEAD");
    #if ($str == "Timeout") {
    #    slackbot($servername.": redis problem");
    #    $fontcolor = "<script type=\"text/javascript\">notify(\"".$servername.": redis problem\");</script>
    #                  <font color=\"red\">";
    #} else {
    #    $fontcolor = "<font color=\"green\">";
    #}

    return $str;
}

function botips($connection)
{
    global $iplistnum;

    $str = ssh2_return($connection, "tail -n 1000000 /var/log/nginx/access.log |
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

function sigHandler($signo)
{
    global $stop_server;
    switch ($signo) {
        case SIGTERM:
            $stop_server = true;
            common_log("SIGTERM stop");
            break;

        case SIGPIPE:
            $stop_server = true;
            common_log("SIGPIPE stop");
            break;

        default:
            break;
    }
}

function errHandler($errno, $errmsg, $filename, $linenum)
{
    global $error_log;
    global $servername;
    $date = date('Y-m-d H:i:s (T)');
    $f = fopen($error_log, 'a');
    if (!empty($f)) {
        $filename  = str_replace($_SERVER['DOCUMENT_ROOT'], '', $filename);
        fwrite($f, "$date: server: $servername: $errmsg - $filename - $linenum\r\n");
        fclose($f);
    }
}

function ssh_disconnect()
{
    common_log("SSH disconnect");
}

function ssh_ignore()
{
    common_log("SSH ignore");
}

function ssh_debug()
{
    common_log("SSH debug");
}

function ssh_macerror()
{
    common_log("SSH macerror");
}
