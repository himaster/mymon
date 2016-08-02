<?php

require_once 'config.php';
require_once 'functions.php';

if (($_SERVER['HTTP_HOST'] == "mymon.pkwteile.de") || ($_SERVER['HTTP_HOST'] == "tmymon.pkwteile.de")) {
    $env="master";
} elseif ($_SERVER['HTTP_HOST'] == "mymon.loc") {
    $env="dev";
} else {
    header("Location: ".$hostname);
}

if (!isset($_GET['task'])) {
    $_GET['task'] = "NULL";
}

if ($_GET['task'] == "exit") {
    setcookie(
        'mymon[login]',
        '',
        time()-604800,
        dirname($_SERVER['PHP_SELF']),
        $_SERVER['HTTP_HOST'],
        $isSecure,
        true
    );
    setcookie(
        'mymon[password]',
        '',
        time()-604800,
        dirname($_SERVER['PHP_SELF']),
        $_SERVER['HTTP_HOST'],
        $isSecure,
        true
    );
    unset($_COOKIE['mymon']);
    header("Location: ".$hostname);
    die();
}

if ($env == "master") {
    $host="127.0.0.1";
} else {
    $host="188.138.234.38";
}
$dbconnection = new mysqli($host, "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");
$result = $dbconnection->query("SELECT `id`, `name`
								FROM `mymon`.`roles`") or die($dbconnection->error);
while ($row = $result->fetch_assoc()) {
    $roles[intval($row['id'])] = $row['name'];
}
if (isset($_COOKIE["mymon"])) {
    $login = no_injection($_COOKIE["mymon"]["login"]);
    $password = no_injection($_COOKIE["mymon"]["password"]);
    $result = $dbconnection->query("SELECT id, login, password, email, la, rep, loc, `500`, el, mon, red, notify
                                    FROM `mymon`.`users`
                                    WHERE login ='" .$login. "'
                                    AND password='" .$password. "'
                                    AND approvied='1'
                                    LIMIT 1") or die($dbconnection->error);
    $result_assoc = $result->fetch_assoc();
    $uid = $result_assoc['id'];
    $umail = $result_assoc['email'];
    $ula = $result_assoc['la'];
    $urep = $result_assoc['rep'];
    $uloc = $result_assoc['loc'];
    $u500 = $result_assoc['500'];
    $uel = $result_assoc['el'];
    $umon = $result_assoc['mon'];
    $ured = $result_assoc['red'];
    $unotify = $result_assoc['notify'];

    if ($result->num_rows == 1) {
        $result1 = $dbconnection->query("SELECT *
                                         FROM `mymon`.`user_roles`
                                         WHERE `user_id` = {$uid}
                                         AND `role_id` = 1") or die($dbconnection->error);
        if ($result1->num_rows == 1) {
            $isAdmin = true;
        } else {
            $isAdmin = false;
        }
        switch ($_GET["task"]) {
            case "500err":
                include "header.html";
                include "500.php";
                include "footer.html";
                break;

            case "editor":
                if (!$isAdmin) {
                    die("You have not rights.");
                }
                include "header.html";
                include "editor.php";
                include "footer.html";
                break;

            case "users_editor":
                if (!$isAdmin) {
                    die("You have not rights.");
                }
                include "header.html";
                include "users.php";
                include "footer.html";
                break;

            case "botips":
                //header("Refresh: 30");
                include "header.html";
                include "botips.php";
                include "footer.html";
                break;

            case "whois":
                include "header.html";
                include "whois.php";
                include "footer.html";
                break;

            case "editor_save":
                include "editor_save.php";
                break;

            case "editor_remove":
                include "editor_remove.php";
                break;

            case "users_editor_save":
                include "users_editor_save.php";
                break;

            case "user_remove":
                echo "User id: ".$_GET['user_id'];
                $query = "DELETE FROM `mymon`.`users` WHERE `id` = '".$_GET['user_id']."'";
                if ($result = $dbconnection->query($query)) {
                    echo "Successfully removed.";
                } else {
                    var_dump($dbconnection->error);
                }
                break;

            case "replica_repair":
                include "replica_repair.php";
                break;

            case "replica_restart":
                include "replica_restart.php";
                break;

            case "top":
                header("Refresh: 5");
                include "header.html";
                include "top.php";
                include "footer.html";
                break;

            case 'getdata':
                include "getdata.php";
                break;

            case "confirm":
                include "confirm.php";
                break;

            case "sendmsg":
                include "sendmsg.php";
                break;

            case "msgred":
                $result = $dbconnection->query("UPDATE `mymon`.`messages`
                                                SET isRead = 1
                                                WHERE receiver ='$uid'
                                                AND isRead = 0
                                                AND isDeleted = 0
                                                LIMIT 1;") or
                die($dbconnection->error());
                break;

            case "ban_ip":
                include "ban_ip.php";
                break;

            case "unban_ip":
                include "unban_ip.php";
                break;

            case "slow_querys":
                include "header.html";
                include "slow_querys.php";
                include "footer.html";
                break;

            default:
                setcookie(
                    'mymon[login]',
                    $login,
                    time()+604800,
                    dirname($_SERVER['PHP_SELF']),
                    $_SERVER['HTTP_HOST'],
                    $isSecure,
                    true
                );
                setcookie(
                    'mymon[password]',
                    $password,
                    time()+604800,
                    dirname($_SERVER['PHP_SELF']),
                    $_SERVER['HTTP_HOST'],
                    $isSecure,
                    true
                );
                include "header.html";
                include "table.php";
                include "footer.html";
                break;
        }
        if (isset($dbconnection)) {
            unset($dbconnection);
        }
    } else {
        echo 'Wrong login/password in cookies???';
    }
} elseif (isset($_POST['auth_submit'])) {
    $login = no_injection($_POST['login']);
    $password = md5(no_injection($_POST['password']));
    $result = $dbconnection->query("SELECT id, login, password, email, la, rep, loc, `500`, el, mon, red, notify
                                    FROM `mymon`.`users`
                                    WHERE login ='" .$login. "'
                                    AND password='" .$password. "'
                                    AND approvied='1'
                                    LIMIT 1") or die($dbconnection->error);
    $result_assoc = $result->fetch_assoc();
    $uid = $result_assoc['id'];
    $umail = $result_assoc['email'];
    $ula = $result_assoc['la'];
    $urep = $result_assoc['rep'];
    $uloc = $result_assoc['loc'];
    $u500 = $result_assoc['500'];
    $uel = $result_assoc['el'];
    $umon = $result_assoc['mon'];
    $ured = $result_assoc['red'];
    $unotify = $result_assoc['notify'];
    if ($result->num_rows == 1) {
        $result1 = $dbconnection->query("SELECT *
                                         FROM `mymon`.`user_roles`
                                         WHERE `user_id` = {$uid}
                                         AND `role_id` = 1") or die($dbconnection->error);
        if ($result1->num_rows == 1) {
            $isAdmin = true;
        } else {
            $isAdmin = false;
        }
        setcookie(
            'mymon[login]',
            $login,
            time()+604800,
            dirname($_SERVER['PHP_SELF']),
            $_SERVER['HTTP_HOST'],
            $isSecure,
            true
        );
        setcookie(
            'mymon[password]',
            $password,
            time()+604800,
            dirname($_SERVER['PHP_SELF']),
            $_SERVER['HTTP_HOST'],
            $isSecure,
            true
        );
        include "header.html";
        include "table.php";
        include "footer.html";
    } else {
        include "header.html";
        echo 'Wrong name/password.';
    }
} else {
    include "header.html";
    include "auth.html";
    include "footer.html";
}
