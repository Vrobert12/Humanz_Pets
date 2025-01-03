<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Mail Password</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
<?php
session_start();
 if (isset($_SESSION['token']) && isset($_GET['logToken'])) {
    if ($_SESSION['token'] == $_GET['logToken']) {


        $_SESSION['token'] = $verification_token = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
        $_SESSION['title'] = "Reseting your password";
        $_SESSION['backToMail']="logIn.php";
        $_SESSION['previousPage']="resetPassword-mail.php";

    }else {
        header('location:' . $_SESSION['previousPage']);
        $_SESSION['token'] = $verification_token = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

        $_SESSION['title'] =  $_SESSION['token'] ;
        exit();
    }
}
elseif(isset($_SESSION['previousPage'])){
    header('location:' . $_SESSION['previousPage']);
    $_SESSION['token'] = $verification_token = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

    $_SESSION['title'] =  $_SESSION['token'] ;
    exit();

}


?><form method="post" action="functions.php" class="mainForm">
<label for="mail">E-mail</label><br>
<input type="email" placeholder="Emailcím" name="mailReset" class="inputok" id="mail"><br>

<input type="submit" name="action" value="Send" class="inputok"><br><br>
<?php


$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
if (isset($_SESSION['message'])) {
    if ($_SESSION['message'] == "Most már be bír jelentkezni") {
        echo "<p class='success'>" . $message . "</p>";
    } else {
        echo "<p class='warning'>" . $message . "</p>";
    }

}

?></form>


</body>
</html>