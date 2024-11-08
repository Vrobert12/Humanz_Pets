<!DOCTYPE html>
<html lang="en">
<head>
    <title>Main Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="LogOut.js"></script>
    <script src="indexJS.js"></script>
    <link rel="stylesheet" href="style.css">

    <!-- Add custom CSS for the popup with animation -->
    <style>

    </style>

</head>
<body>

<?php
session_start();
if(isset($_SESSION['message'])){
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}
$lang = $_GET['lang'] ?? $_SESSION['lang']  ??'en';
if(isset($_GET['lang'])){
    $_SESSION['lang'] = $_GET['lang'];
}
include_once "lang_$lang.php";
?>
<form method="post" action="functions.php" class="mainForm">
    <a class="nextPage" href="index.php"><?php echo BACK?></a><br><br>
    <label for="mail">E-mail</label><br>
    <input type="email" placeholder="Email" name="mail" class="inputok" id="mail"><br>
    <label for="pass"><?php echo PASSWORD?></label><br>
    <input type="password" placeholder="********" name="pass" class="inputok" id="pass"><br>

    <input type="submit" name="submit" value="<?php echo LOGIN?>" class="inputok"><br><br>
    <input type="hidden" name="action" value="Log in" class="inputok">

    <label for="mail"><?php echo NOACC?></label><br><br>
    <a href="registration.php"><?php echo REGHERE?></a><br>


</form>
</body>
</html>