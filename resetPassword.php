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


$autoload = new Functions();
$autoload->checkAutoLogin();
$connection = $autoload->connect($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $pdoOptions);
if(isset($_SESSION['email'])) {
    if (!isset($_GET['mail'])) {
        $_SESSION['message']="no mail specified";
        header('Location:index.php');
        exit();
    }
}
// Ensure token and email are set in $_GET
elseif (!isset($_GET['mail']) || !isset($_GET['token'])) {
    $_SESSION['message']="no mail or token specified";
    header('Location:index.php');
    exit();
}
if(isset($_GET['token'])) {
    $email = trim($_GET['mail']);
    $token = trim($_GET['token']);
$_SESSION['backPic']="http://localhost/Humanz_Pets/resetPassword.php?mail=" . $email . "&token=" . $token;
// Fetch verification code for the given email
    $stmt = "SELECT verification_code FROM veterinarian WHERE veterinarianMail = :email";
    $stmt = $connection->prepare($stmt);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        die("No result found for email: $email");
    }

    if ($token != $result['verification_code']) {
        die("Token mismatch. URL token: $token, DB token: " . $result['verification_code']);
    }

    if (isset($_SESSION['email']) && $_SESSION['email'] != $email) {
        die("Session email mismatch. Session: " . $_SESSION['email'] . ", URL email: $email");
    }
}
// All checks passed
echo "Verification successful!";

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
<form method="post" action="functions.php" class="mainForm">

    <input type="hidden" placeholder="Emailcím" name="mail" class="inputok" id="mail" value="<?php
    if(isset($_SESSION['email'])){echo $_SESSION['email'];}

    ?>"><br>
    <?php

    echo '<input type="hidden" name="mail" value="'.$_GET['mail'].'" class="inputok"><br><br>';
    ?>
    <label><b>Change Your password.</b></label><br><br>
    <label for="password">Your new password:</label><br>
    <input type="password" class="inputok" placeholder="********" name="resetPassword"  id="pass"><br>
    <label for="confirmPassword">Confirm your new password:</label><br>
    <input type="password" class="inputok" placeholder="********" name="confirmPassword"  id="pass2"><br>
    <input type="hidden" name="action" value="resetPass" class="inputok"><br><br>

    <input type="submit"  value="ResetPass" class="inputok"><br><br>

    <?php

    $message=isset($_SESSION['message']) ? $_SESSION['message']:'';
    if(isset($_SESSION['message'])) {
        echo "<p class='success'>" . $message . "</p>";


    }?>

</form>
</body>
</html>