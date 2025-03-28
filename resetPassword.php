<?php

include "functions.php";
$autoload=new Functions();
$lang=$autoload->language();
$autoload->checkAutoLogin();
$connection = $autoload->connect($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $pdoOptions);
if(isset($_SESSION['email'])) {
    if (!isset($_GET['mail'])) {
        $_SESSION['message']="no mail specified";
        header('Location:index.php');
        exit();
    }
    $email=$_GET['mail'];
}
// Ensure token and email are set in $_GET
elseif ((!isset($_GET['verify_email']) || !isset($_GET['verification_code']))) {
    $_SESSION['message']="no mail or token specified";
    header('Location:index.php');
    exit();
}
if(isset($_GET['verification_code'])) {
    $email = trim($_GET['verify_email']);
    $token = trim($_GET['verification_code']);
$_SESSION['backPic']="http://localhost/Humanz_Pets/resetPassword.php?mail=" . $email . "&token=" . $token;
// Fetch verification code for the given email
    $stmt = "SELECT verification_code FROM veterinarian WHERE veterinarianMail = :email";
    $stmt = $connection->prepare($stmt);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $vetResult = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = "SELECT verification_code FROM user WHERE userMail = :email";
    $stmt = $connection->prepare($stmt);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $userResult = $stmt->fetch(PDO::FETCH_ASSOC);
    $email=$_GET['verify_email'];
    if (!$vetResult and !$userResult) {
        header('Location:index.php');
        exit();
    }

    if (isset($_SESSION['email']) && $_SESSION['email'] != $email) {
        die("Session email mismatch. Session: " . $_SESSION['email'] . ", URL email: $email");
    }
}

if(isset($_GET['verification_code'])) {
    $email = trim($_GET['verify_email']);
    $passwordValidation = trim($_GET['verification_code']);

// Set the back picture URL in session
    $_SESSION['backPic'] = "http://localhost/Humanz_Pets/resetPassword.php?mail=" . $email . "&passwordValidation=" . $passwordValidation;

// Check for validation details in the `user` table
    $stmt = "SELECT passwordValidation FROM user WHERE userMail = :email and passwordValidation = :passwordValidation";
    $stmt = $connection->prepare($stmt);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':passwordValidation', $passwordValidation, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

// If not found in `user`, check the `veterinarian` table
    if (!$result) {
        $stmt = "SELECT passwordValidation FROM veterinarian WHERE veterinarianMail = :email and passwordValidation = :passwordValidation";
        $stmt = $connection->prepare($stmt);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':passwordValidation', $passwordValidation, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }

// If still not found, return an error
    if (!$result) {
        header('Location:index.php');
        exit();
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
<body style="background: #659df7">
<form method="post" action="functions.php" class="mainForm">

    <input type="hidden" placeholder="Emailcím" name="mail" class="inputok" id="mail" value="<?php
    if(isset($_SESSION['email'])){echo $_SESSION['email'];}

    ?>"><br>
    <?php

    echo '<input type="hidden" name="mail" value="'.$email.'" class="inputok"><br><br>';
    ?>
    <label><b><?php echo CHANGEPS?></b></label><br><br>
    <label for="password"><?php echo NEWPASS?>:</label><br>
    <input type="password" class="inputok" placeholder="********" name="resetPassword"  id="pass"><br>
    <label for="confirmPassword"><?php echo NEWPASSCONF?>:</label><br>
    <input type="password" class="inputok" placeholder="********" name="confirmPassword"  id="pass2"><br>
    <input type="hidden" name="action" value="resetPass" class="inputok"><br><br>

    <input type="submit"  value="<?php echo PASSSET?>" class="inputok"><br><br>

    <?php

    $message=isset($_SESSION['message']) ? $_SESSION['message']:'';
    if(isset($_SESSION['message'])) {
        echo "<p class='success'>" . $message . "</p>";


    }?>

</form>
</body>
</html>