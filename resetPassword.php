<?php

include "functions.php";
$autoload=new Functions();
$lang=$autoload->language();
include "lang_$lang.php";
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
elseif ((!isset($_GET['mail']) || !isset($_GET['token'])) && (!isset($_GET['mail']) || !isset($_GET['passwordValidation']))) {
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

if(isset($_GET['passwordValidation'])) {
    $email = trim($_GET['mail']);
    $passwordValidation = trim($_GET['passwordValidation']);

// Set the back picture URL in session
    $_SESSION['backPic'] = "http://localhost/Humanz_Pets/resetPassword.php?mail=" . $email . "&passwordValidation=" . $passwordValidation;

// Check for validation details in the `user` table
    $stmt = "SELECT passwordValidation FROM user WHERE userMail = :email";
    $stmt = $connection->prepare($stmt);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

// If not found in `user`, check the `veterinarian` table
    if (!$result) {
        $stmt = "SELECT passwordValidation FROM veterinarian WHERE veterinarianMail = :email";
        $stmt = $connection->prepare($stmt);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }

// If still not found, return an error
    if (!$result) {
        die("No result found for email: $email");
    }

// Verify the password validation token
    if ($passwordValidation != $result['passwordValidation']) {
        die("Password mismatch. URL token: $passwordValidation, DB token: " . $result['passwordValidation']);
    }

// Verify session email matches the provided email
    if (isset($_SESSION['email']) && $_SESSION['email'] != $email) {
        die("Session email mismatch. Session: " . $_SESSION['email'] . ", URL email: $email");
    }

// If all checks pass, proceed with further operations
    echo "Validation successful! You can proceed to reset the password.";

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