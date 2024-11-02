<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
    <link rel="stylesheet" href="SideStyle.css">
    <?php include "config.php"; ?>
    <style>
        .warning {
            color: red;
        }
        .inputok {
            border-radius: 10px;
            font-size: 20px;
            padding: 10px;
            margin: 10px;
            text-align: center;
        }
        .inputok.error {
            border-color: red;
        }
    </style>
    <script src="validate.js"></script>
</head>
<body>

<?php

if (isset($_SESSION['token']) && isset($_GET['token'])) {
    if ($_SESSION['token'] != $_GET['token']) {
        header('location:' . $_SESSION['previousPage']);
        $_SESSION['title'] = "";
        exit();
    } else {
        $_SESSION['token'] = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
    }
} else {
    echo '<form method="post" action="functions.php" class="mainForm">';
}
if (isset($_SESSION['title'])) {
    echo "<h2 style='color: #2a7e2a'>" . $_SESSION['title'] . "</h2>";
}
?>
<form method="post" action="functions.php" class="mainForm">
    <a class="nextPage" href="logIn.php">Back</a><br><br>
    <label for="fname">First Name:</label><br>
    <input type="text" class="inputok" placeholder="First Name" name="fname" id="fname" ><br>
    <label for="lname">Last Name:</label><br>
    <input type="text" class="inputok" placeholder="Last Name" name="lname" id="lname" ><br>

    <label for="tel1">Phone Number:</label><br>
    <select name="tel1" class="inputok" id="tel1">
        <option hidden="hidden" value="Number">Number</option>
        <?php
        for ($i=10; $i<=39; $i++){
            echo "<option value=\"0".$i."\">0".$i."</option>";
            if($i==23 || $i==28 ||$i==29 || $i==39){
                echo "<option value=\"0".$i."0\">0".$i."0</option>";
            }
        }
        for ($i=60; $i<=69; $i++){
            echo "<option value=\"0".$i."\">0".$i."</option>";
        }
        ?>
    </select>
    <input type="text" placeholder="Phone Number" name="tel2" class="inputok" id="tel2" ><br>
    <label for="mail">E-mail:</label><br>
    <input type="email" class="inputok" placeholder="Email Address" name="mail" id="mail" ><br>
    <?php
    if (!isset($_GET['token']) || !isset($_SESSION['token'])) {
        $_SESSION['token'] = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
        echo '<label for="pass">Password:</label><br>
        <input type="password" class="inputok" placeholder="********" name="pass" id="pass" ><br>
        <label for="pass2">Confirm Password:</label><br>
        <input type="password" class="inputok" placeholder="********" name="pass2" id="pass2" ><br>
        <input type="submit" class="inputok" name="action" value="registration">';
    } else {
        echo '<input type="submit" class="inputok" name="action" value="AddWorker">';
    }

    $message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
    echo "<p class='warning'>" . $message . "</p>";
    ?>
</form>
</body>
</html>
