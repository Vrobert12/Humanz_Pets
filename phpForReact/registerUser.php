<?php
global $pdo;
require_once 'react_config.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


include "../functions.php";
$functions=new Functions();
$lang=$functions->language();

header("Content-Type: application/json");

try {

    // Read JSON input
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if all fields are provided
    if (!isset($data['firstname'], $data['lastname'], $data['phone'], $data['email'], $data['language'], $data['password'])) {
        echo json_encode(["success" => false, "message" => "Missing required fields"]);
        exit();
    }

    // Trim and sanitize inputs
    $firstname = htmlspecialchars(trim($data['firstname']));
    $lastname = htmlspecialchars(trim($data['lastname']));
    $phone = htmlspecialchars(trim($data['phone']));
    $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
    $language = trim($data['language']);
    if ($language == 'English') {
        $language = 'en';
    } elseif ($language == 'Hungarian') {
        $language = 'hu';
    } else {
        $language = 'sr';
    }
    $password = password_hash(trim($data['password']), PASSWORD_BCRYPT);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Invalid email format"]);
        exit();
    }

    // Check if the email already exists
    $checkQuery = "SELECT userId FROM user WHERE userMail = :email";
    $stmt = $pdo->prepare($checkQuery);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => false, "message" => "Email already in use"]);
        exit();
    }
    $verification_code = substr(number_format(time() * rand(), 0, '',
        ''), 0, 7);
    $verify = 0;
    $profilePic = 'loginPic.png';
    $privilage = 'User';
    $time = time();
    $currentTime = date("Y-m-d H:i:s", $time);
    $verifyTime = time() + 60 * 10;
    $verification_time = date("Y-m-d H:i:s", $verifyTime);
    $banned = 0;
    $banned_time = null;
    $verification_code_expire = null;
    $verification_code_pass = null;

    // Insert new user
    $insertQuery = "INSERT INTO user (firstname, lastname, phoneNumber, userMail, usedLanguage, userPassword, verification_code, verify, profilePic, privilage, registrationTime, verification_time, banned, banned_time, passwordValidation, passwordValidationTime) 
                    VALUES (:firstname, :lastname, :phone, :email, :language, :password, :verification_code, :verify, :profilePic, :privilage, :registrationTime, :verification_time, :banned, :banned_time, :passwordValidation, :passwordValidationTime)";
    $stmt = $pdo->prepare($insertQuery);

    $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
    $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':language', $language, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->bindParam(':verification_code', $verification_code, PDO::PARAM_STR);
    $stmt->bindParam(':verify', $verify, PDO::PARAM_STR);
    $stmt->bindParam(':profilePic', $profilePic, PDO::PARAM_STR);
    $stmt->bindParam(':privilage', $privilage, PDO::PARAM_STR);
    $stmt->bindParam(':registrationTime', $currentTime, PDO::PARAM_STR);
    $stmt->bindParam(':verification_time', $verification_time, PDO::PARAM_STR);
    $stmt->bindParam(':banned', $banned, PDO::PARAM_STR);
    $stmt->bindParam(':banned_time', $banned_time, PDO::PARAM_STR);
    $stmt->bindParam(':passwordValidation', $verification_code_pass, PDO::PARAM_STR);
    $stmt->bindParam(':passwordValidationTime', $verification_code_expire, PDO::PARAM_STR);


    $result = $stmt->execute();

    if ($result) {

        $mail = new PHPMailer(true);
        try {

            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'robertvarro12@gmail.com';
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $here = '';
            if($language === 'en'){$here = 'Here';}
            elseif ($language === 'hu'){$here = 'Itt';}
            else{$here = 'Ovde';}

            $mail->CharSet = 'UTF-8';

            $registration_link = '<a href="http://localhost/Humanz2.0/Humanz_Pets/email-verification.php?
                    verification_code=' . $verification_code . '&verify_email=' . $email . '">'.$here.'</a>';

            $mail->setFrom("robertvarro12@gmail.com", 'R&D Veterinary');
            $mail->addAddress($email, $email);
            $mail->addReplyTo("robertvarro12@gmail.com", 'Reply');
            $mail->Subject = "R&D";
            $mail->Body = "<h2>Validate</h2> Your link:<br>".$registration_link;
            $mail->AltBody = "Your link:<br>".$registration_link;

        } catch
        (Exception $e) {

            $_SESSION['message'] = "Message could not be sent. Mailer Error: " . $e->getMessage();
        }
//    if (isset($_SESSION["reservation"])) {
//
//        $mail->addAddress($_SESSION["email"], $_SESSION['name']);
//        $mail->addReplyTo('info@example.com', 'Information');
//        $mail->addCC('cc@example.com');
//        $mail->addBCC('bcc@example.com');
//        $mail->Subject = "R&D";
//
//        $_SESSION['message'] = "<b>You have reserved table " . $_SESSION['reservationTable'] . "</b>";
//        $mail->Body = "<h2>You have reserved our table </h2>  Reservation is on day <b>" . $_SESSION['day'] . "</b> from <b>"
//            . $_SESSION['reservationTime'] . "</b> to <b>" . $_SESSION['reservationTimeEnd'] . ". Your reservation code is: " . $_SESSION['reservationCode'] . "</b>";
//        $mail->AltBody = "<h2>You have reserved our table </h2>  Reservation is on day <b>" . $_SESSION['day'] . "</b> from <b>"
//            . $_SESSION['reservationTime'] . "</b> to <b>" . $_SESSION['reservationTimeEnd'] . ". Your reservation code is: " . $_SESSION['reservationCode'] . "</b>";
//        unset($_SESSION['workerEmail']);
//        header('Location:reservation.php?table=' . $_SESSION['reservationTable']);
//    }
        $mail->send();
        if (isset($_POST['mail']))
            $_POST['mail'] = $_SESSION['email'];
        //exit();

        echo json_encode(["success" => true, "message" => "Registered successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
} catch (Exception $e) {
}

