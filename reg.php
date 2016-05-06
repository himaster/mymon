<?php
include_once("functions.php");

error_reporting(E_ALL);

$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");

if (isset($_POST['submit'])) {
    if (empty($_POST['login'])) {
        echo 'Вы не ввели логин';
    } elseif (empty($_POST['password'])) {
        echo 'Вы не ввели пароль';
    } elseif (empty($_POST['password2'])) {
        echo 'Вы не ввели подтверждение пароля';
    } elseif ($_POST['password'] != $_POST['password2']) {
        echo 'Введенные пароли не совпадают';
    } elseif (empty($_POST['email'])) {
        echo 'Вы не ввели E-mail';
    } else {
        $login = no_injection($_POST['login']);
        $password = md5(no_injection($_POST['password']));
        $email = no_injection($_POST['email']);
        $result = $dbconnection->query("SELECT `id` FROM `users` WHERE `login`='{$login}'") or die($dbconnection->error());
        if ($result->num_rows > 0) {
            echo 'Такой логин уже существует';
        } else {
            $result = $dbconnection->query("INSERT INTO users(login , password , email, approvied) VALUES ('$login', '$password', '$email', '0')") or die($dbconnection->error());
            $msg = "<html><head><title></title></head><body>";
            $msg .= "User $login ($email) just registered. <form action='https://mymon.pkwteile.de/index.php' method='get'>";
            $msg .= "<input type='hidden' name='task' value='confirm' />";
            $msg .= "<input type='hidden' name='username' value=$login />";
            $result = $dbconnection->query("SELECT `id`, `name` FROM `roles`") or die($dbconnection->error());
            while ($row = $result->fetch_assoc()) {
                $msg .= "<p>".$row['name']." <input type='checkbox' name='".$row['name']."' />";
            }
            $msg .= "<p>Click <input type='submit' value='here' /> to confirm.";
            $msg .= "</form></body></html>";
            $to = "himaster@mailer.ag";
            $subject = "Mymon registration";
            $headers = "From: mymon@pkwteile.de\r\n";
            $headers .= "Reply-To: himaster@mailer.ag\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            mail($to, $subject, $msg, $headers);
            echo "Регистрация успешно прошла. Ожидайте письма с подтверждением.";
        }
    }
} elseif (isset($_POST['submit_edit'])) {
    if (empty($_POST['password'])) {
        echo 'Вы не ввели пароль';
    } elseif (empty($_POST['password2'])) {
        echo 'Вы не ввели подтверждение пароля';
    } elseif ($_POST['password'] != $_POST['password2']) {
        echo 'Введенные пароли не совпадают';
    } elseif (empty($_POST['email'])) {
        echo 'Вы не ввели E-mail';
    } else {
        $login = no_injection($_POST['login']);
        $password = md5(no_injection($_POST['password']));
        $email = no_injection($_POST['email']);
        if (isset($_POST['la'])) {
            $ula = 1;
        } else {
            $ula = 0;
        }
        if (isset($_POST['rep'])) {
            $urep = 1;
        } else {
            $urep = 0;
        }
        if (isset($_POST['loc'])) {
            $uloc = 1;
        } else {
            $uloc = 0;
        }
        if (isset($_POST['500'])) {
            $u500 = 1;
        } else {
            $u500 = 0;
        }
        if (isset($_POST['el'])) {
            $uel = 1;
        } else {
            $uel = 0;
        }
        if (isset($_POST['mon'])) {
            $umon = 1;
        } else {
            $umon = 0;
        }
        if (isset($_POST['red'])) {
            $ured = 1;
        } else {
            $ured = 0;
        }
        if (isset($_POST['notify'])) {
            $unotify = 1;
        } else {
            $unotify = 0;
        }
        $query = "UPDATE users SET password = '$password', email = '$email', la = '$ula', rep = '$urep', loc = '$uloc', `500` = '$u500', el = '$uel', mon = '$umon', red = '$ured', notify = '$unotify' WHERE login = '$login'";
        $result = $dbconnection->query($query) or die(mysql_error());
        header("Refresh:0; url=index.php");
    }
} else {
    echo "None selected";
}
