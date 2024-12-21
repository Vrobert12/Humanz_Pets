<?php
include 'config.php';
 function connect($dsn, $pdoOptions): PDO
 {
        try {
            $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $pdoOptions);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
        return $pdo;
    }
    $connection = connect($dsn, $pdoOptions);
if (isset($_POST['verify_email']) && isset($_SESSION['mailReset'])) {
    $sql = "SELECT passwordValidation, passwordValidationTime FROM user WHERE userMail = :email";
    $stmtTeszt = $GLOBALS['connection']->prepare($sql);
    $stmtTeszt->bindParam(':email', $_SESSION['email'], PDO::PARAM_STR);
    $stmtTeszt->execute();
    $result = $stmtTeszt->fetchAll(PDO::FETCH_ASSOC);
    $time = time();
    $check_time = date("Y-m-d H:i:s", $time);

    if (count($result) > 0) {
        foreach ($result as $rows) {
            if ($rows['passwordValidationTime'] <= $check_time) {
                $mail = $_SESSION['email'];
                $time = time() + 60 * 10;
                $verification_time = date("Y-m-d H:i:s", $time);
                $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
                $query = $GLOBALS['connection']->prepare("UPDATE user SET passwordValidation = :code, passwordValidationTime = :v_time WHERE userMail = :mail");
                $query->bindParam(':code', $verification_code, PDO::PARAM_STR);
                $query->bindParam(':v_time', $verification_time, PDO::PARAM_STR);
                $query->bindParam(':mail', $mail, PDO::PARAM_STR);
                $query->execute();

                $_SESSION['message'] = "Validation time has expired.";
                header('Location: mail.php');
                exit();
            } else {
                if ($rows['passwordValidation'] == $_POST['verification_code']) {
                    $_SESSION['message'] = "Now you can change the password.";
                    header('Location: resetPassword.php');
                    exit();
                } else {
                    $_SESSION['message'] = "This code is not valid on our page.";
                }
            }
        }
    }
}

if (isset($_POST['verify_email']) && isset($_SESSION['email'])) {
    $sql = "SELECT verification_time, verification_code FROM user WHERE userMail = :email";
    $stmtTeszt = $GLOBALS['connection']->prepare($sql);
    $stmtTeszt->bindParam(':email', $_SESSION['email'], PDO::PARAM_STR);
    $stmtTeszt->execute();
    $result = $stmtTeszt->fetchAll(PDO::FETCH_ASSOC);
    $time = time();
    $check_time = date("Y-m-d H:i:s", $time);

    if (count($result) > 0) {
        foreach ($result as $rows) {
            if ($rows['verification_time'] <= $check_time) {
                $mail = $_SESSION['email'];
                $time = time() + 60 * 10;
                $verification_time = date("Y-m-d H:i:s", $time);
                $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
                $query = $GLOBALS['connection']->prepare("UPDATE user SET verification_code = :code, verification_time = :v_time WHERE userMail = :mail");
                $query->bindParam(':code', $verification_code, PDO::PARAM_STR);
                $query->bindParam(':v_time', $verification_time, PDO::PARAM_STR);
                $query->bindParam(':mail', $mail, PDO::PARAM_STR);
                $query->execute();

                $_SESSION['message'] = "This code is not valid on our page.<br> If you are registered, we sent you an email with a new code.";
                $_SESSION['verification_code'] = $verification_code;
                sleep(2);

                $mail = $_SESSION['email'];
                $logType = "E-mail validation";
                $errorText = "Time for validation has expired";
                $logMessage = $_SESSION['message'];
                errorLogInsert($logType, $mail, $errorText, $logMessage);

                header('Location: mail.php');
                exit();
            } else {
                if ($rows['verification_code'] == $_POST['verification_code']) {
                    $email = $_POST['email'];
                    $verification_code = $_POST['verification_code'];

                    $sql = "UPDATE user SET verify = 1 WHERE userMail = :email AND verification_code = :code";
                    $stmt = $GLOBALS['connection']->prepare($sql);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':code', $verification_code, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        sleep(2);
                        $_SESSION['message'] = "Log in and finish setting up your account";
                        header('Location: logIn.php');
                        exit();
                    } else {
                        sleep(2);
                        $_SESSION['message'] = "Verification failed";
                    }
                } else {
                    sleep(2);
                    $_SESSION['message'] = "This code is not valid on our page.";
                    $mail = $_SESSION['email'];
                    $logType = "E-mail validation";
                    $errorText = "The validation code is not correct!";
                    $logMessage = $_SESSION['message'];
                    errorLogInsert($logType, $mail, $errorText, $logMessage);
                    header('Location: email-verification.php');
                    exit();
                }
            }
        }
    }
}

function errorLogInsert($logType, $mail, $errorText, $logMessage)
{
    sleep(2);
    $time = time();
    $currentTime = date("Y-m-d H:i:s", $time);

    $sql = "INSERT INTO errorlog (errorType, errorMail, errorText, errorTime) VALUES (:type, :mail, :text, :time)";
    $stmt = $GLOBALS['connection']->prepare($sql);

    if (!$stmt) {
        die('Error in SQL query: ' . $GLOBALS['connection']->errorInfo()[2]);
    }

    $stmt->bindParam(':type', $logType, PDO::PARAM_STR);
    $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
    $stmt->bindParam(':text', $errorText, PDO::PARAM_STR);
    $stmt->bindParam(':time', $currentTime, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $_SESSION['message'] = $logMessage;
    } else {
        $_SESSION['message'] = "NUAH.";
    }
}
?>
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
<form method="post" action="email-verification.php" class="mainForm">
    <label for="verification_code">Enter your code.</label><br>
    <input type="hidden" name="email" class="inputok"
           value="<?php if (isset($_SESSION['email'])) echo htmlspecialchars($_SESSION['email']); ?>">
    <input type="text" name="verification_code" class="inputok" placeholder="Enter verification code"><br>
    <input type="submit" name="verify_email" class="inputok" value="Verify Email">


    <?php


    $message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
    if (isset($_SESSION['message'])) {
        echo "<p class='success'>" . $message . "</p>";

echo $_SESSION['mail'];
    } ?>


</form>
</body>
</html>
