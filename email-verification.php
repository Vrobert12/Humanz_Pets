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
if (isset($_GET['verify_email']) && isset($_SESSION['mailReset'])) {
    $email = $_SESSION['mailReset'];
    $verificationCode = $_GET['verification_code'];
    $currentTime = date("Y-m-d H:i:s");

    // Fetch validation details from both tables
    $result = getValidationDetails($email, 'user');
    if (!$result) {
        $result = getValidationDetails($email, 'veterinarian');
    }

    if ($result) {
        error_log("Verification Details: " . print_r($result, true));
        if ($result['passwordValidationTime'] <= $currentTime) {
            $email=$_GET['verify_email'];
            $_SESSION['email'] = $email;
            $_SESSION['test'] = 1;
            $_SESSION['registrationLink'] = '<a href="http://localhost/Humanz_Pets/email-verification.php?
                    verification_code=' . $verificationCode.'&verify_email='.$email.'">here</a>';
            $_SESSION['message'] = VALIDEXP;
            header('Location: mail.php');
            exit();
        }

        if ($result['passwordValidation'] == $verificationCode) {
            $_SESSION['message'] = CHANGEPASS;
            header("Location: resetPassword.php?mail=$email&passwordValidation={$result['passwordValidation']}");
            exit();
        } else {
            $_SESSION['message'] = CODENOVALID.$result['passwordValidation']." ".$verificationCode;
        }
    } else {
        error_log("Email not registered: $email");
        $_SESSION['message'] = EMAILNOTREG;
    }

    header('Location: logIn.php');
    exit();
}

function getValidationDetails($email, $table)
{
    try {
        $sql = "SELECT passwordValidation, passwordValidationTime FROM $table WHERE {$table}Mail = :email";
        $stmt = $GLOBALS['connection']->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}



if (isset($_GET['verify_email']) && isset($_GET['verification_code'])) {
    $email = $_GET['verify_email'];
    $sql = "SELECT verification_time, verification_code FROM user WHERE userMail = :email";
    $stmtTeszt = $GLOBALS['connection']->prepare($sql);
    $stmtTeszt->bindParam(':email',  $email, PDO::PARAM_STR);
    $stmtTeszt->execute();
    $result = $stmtTeszt->fetchAll(PDO::FETCH_ASSOC);
    $time = time();
    $check_time = date("Y-m-d H:i:s", $time);

    if (count($result) > 0) {
        foreach ($result as $rows) {
            if ($rows['verification_time'] <= $check_time) {
                $time = time() + 60 * 10;
                $verification_time = date("Y-m-d H:i:s", $time);
                $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
                $query = $GLOBALS['connection']->prepare("UPDATE user SET verification_code = :code, verification_time = :v_time WHERE userMail = :mail");
                $query->bindParam(':code', $verification_code, PDO::PARAM_STR);
                $query->bindParam(':v_time', $verification_time, PDO::PARAM_STR);
                $query->bindParam(':mail', $email, PDO::PARAM_STR);
                $query->execute();

                $_SESSION['message'] = CODENOVALID;
                $_SESSION['registrationLink'] = '<a href="http://localhost/Humanz_Pets/email-verification.php?
                    verification_code=' . $verification_code.'&verify_email='.$email.'">here</a>';

                sleep(2);

                $mail = $_SESSION['email'];
                $logType = "E-mail validation";
                $errorText = "Time for validation has expired";
                $logMessage = $_SESSION['message'];
                errorLogInsert($logType, $email, $errorText, $logMessage);
                $_SESSION['test'] = 2;
                header('Location: mail.php');
                exit();
            } else {
                if ($rows['verification_code'] == $_GET['verification_code']) {
                    $verification_code = $_GET['verification_code'];

                    $sql = "UPDATE user SET verify = 1 WHERE userMail = :email AND verification_code = :code";
                    $stmt = $GLOBALS['connection']->prepare($sql);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':code', $verification_code, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        sleep(2);
                        $_SESSION['message'] = LOGSET;
                        header('Location: logIn.php');
                        exit();
                    } else {
                        sleep(2);
                        $_SESSION['message'] = VERFAIL;
                    }
                } else {
                    sleep(2);
                    $_SESSION['message'] = CODENOVALID;
                    $mail = $_SESSION['email'];
                    $_SESSION['registrationLink'] = '<a href="http://localhost/Humanz_Pets/email-verification.php?
                    verification_code=' . $verification_code.'&verify_email='.$email.'">here</a>';

                    $logType = "E-mail validation";
                    $errorText = "The validation code is not correct!";
                    $logMessage = $_SESSION['message'];
                    errorLogInsert($logType, $email, $errorText, $logMessage);
                    $_SESSION['test'] = 3;
                    header('Location: mail.php');
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