<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    $lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
    if(isset($_GET['lang'])){
        $_SESSION['lang'] = $_GET['lang'];
    }
    include "lang_$lang.php";
}

include "functions.php";
$autoload=new Functions();
$autoload->checkAutoLogin();
$_SESSION['backPic']='addVet.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Registration</title>
    <link rel="stylesheet" href="style.css">
    <script src="indexJS.js"></script>
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
    <a class="btn btn-secondary" href="logIn.php">Back</a><br><br>
    <label for="fname"><?php echo NAME?>:</label><br>
    <input type="text" class="inputok" placeholder="<?php echo NAME?>" name="fname" id="fname" ><br>
    <label for="lname"><?php echo LASTNAME?>:</label><br>
    <input type="text" class="inputok" placeholder="<?php echo LASTNAME?>" name="lname" id="lname" ><br>

    <label for="tel"><?php echo PHONE?>:</label><br>

    <input type="text" placeholder="Phone Number" name="tel" class="inputok" id="tel" ><br>
    <label for="mail">E-mail:</label><br>
    <input type="email" class="inputok" placeholder="Email" name="mail" id="mail" ><br>
    <?php
    $_SESSION['backPic']='addVet.php';
    $_SESSION['token'] = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
    echo '<input type="submit" class="btn btn-primary" name="action" value="AddVet">';


    $message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
    echo "<p class='warning'>" . $message . "</p>";
    ?>
</form>

</body>
</html>