<?php

require_once 'config.php';
require_once 'functions.php';

$dbconnection = new mysqli("188.138.234.38", "mymon", "eiGo7iek", "mymon") or die($dbconnection->connect_errno."\n");
echo "<div class=\"register\">";
if (isset($_POST['submit'])) {
    if (empty($_POST['login'])) {
        echo 'You have not entered login';
    } elseif (empty($_POST['password'])) {
        echo 'You have not entered password';
    } elseif (empty($_POST['password2'])) {
        echo 'You have not entered password confirmation';
    } elseif ($_POST['password'] != $_POST['password2']) {
        echo 'Entered passwords are not equal';
    } elseif (empty($_POST['email'])) {
        echo 'You have not entered e-mail';
    } else {
        $login = no_injection($_POST['login']);
        $password = md5(no_injection($_POST['password']));
        $email = no_injection($_POST['email']);
        $result = $dbconnection->query("SELECT `id`
                                        FROM `users`
                                        WHERE `login`='{$login}'") or die($dbconnection->error);
        if ($result->num_rows > 0) {
            echo 'This login exists.';
        } else {
            $result = $dbconnection->query("INSERT INTO users(login , password , email, approvied)
                                            VALUES ('$login', '$password', '$email', '0')") or
                        die($dbconnection->error);
            $msg = "<html><head><title></title></head><body>";
            $msg = file_get_contents('header.html');
            $msg .= "User $login ($email) just registered. <form action=\"$hostname\" method='get'>";
            $msg .= "<input type='hidden' name='task' value='confirm' />";
            $msg .= "<input type='hidden' name='username' value=$login />";
            $result = $dbconnection->query("SELECT `id`, `name` FROM `roles`") or die($dbconnection->error);
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
            include 'header.html';
            echo "Registered successfull. Please, wait confirmation letter.";
            include 'footer.html';
        }
    }
} elseif (isset($_POST['submit_edit'])) {
    if (!empty($_POST['password'])) {
        if (empty($_POST['password2'])) {
            die('You have not entered password confirmation');
        } elseif ($_POST['password'] != $_POST['password2']) {
            die('Entered passwords are not equal');
        }
        $password = md5(no_injection($_POST['password']));
        $query = "UPDATE `users` SET `password` = '$password' WHERE login = '$login';";
        $result = $dbconnection->query($query) or die($dbconnection->error);
    }
    $login = no_injection($_POST['login']);
    $email = no_injection($_POST['email']);
    $ula = (isset($_POST['la'])) ? 1 : 0;
    $urep = (isset($_POST['rep'])) ? 1 : 0;
    $uloc = (isset($_POST['loc'])) ? 1 : 0;
    $u500 = (isset($_POST['500'])) ? 1 : 0;
    $uel = (isset($_POST['el'])) ? 1 : 0;
    $umon = (isset($_POST['mon'])) ? 1 : 0;
    $ured = (isset($_POST['red'])) ? 1 : 0;
    $unotify = (isset($_POST['notify'])) ? 1 : 0;
    $query = "UPDATE `users`
              SET `email` = '$email',
                  `la` = '$ula',
                  `rep` = '$urep',
                  `loc` = '$uloc',
                  `500` = '$u500',
                  `el` = '$uel',
                  `mon` = '$umon',
                  `red` = '$ured',
                  `notify` = '$unotify'
              WHERE login = '$login';";
    echo "Query: ".$query;
    $result = $dbconnection->query($query) or die($dbconnection->error);
    header("Refresh:0; url=index.php");
} else {
    echo "None selected";
}
echo "</div>";
