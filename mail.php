<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use Dotenv\Dotenv;

require 'vendor/autoload.php';
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
session_start();
$mail = new PHPMailer(true);

try {

    //Server settings
    $_SESSION['message'] = "";
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'robertvarro12@gmail.com';
    $mail->Password = $_ENV['SMTP_PASSWORD'];
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->setFrom('robertvarro12@gmail.com', 'R&D Veterinary');
    $mail->CharSet = 'UTF-8';
    if(isset($_SESSION['ownerMail']))
    {
        echo $_SESSION['usedLanguage'];
        include "functions.php";
        $autoload=new Functions();
        $lang=$_SESSION['usedLanguage'];
        include "lang_$lang.php";

        $mail->addAddress(  $_SESSION['ownerMail'] ,$_SESSION['ownerMail'] );
        $mail->addReplyTo('info@example.com', 'Information');
        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com');
        $mail->Subject = "R&D";
        $mail->AltBody = REVIEV_MESSAGE . " Visit the review page here: " . $_SESSION['reviewLink'];
        $mail->Body = REVIEV_MESSAGE . " <a href='" . $_SESSION['reviewLink'] . "'>here</a>";


        unset($_SESSION['ownerMail']);
        unset($_SESSION['usedLanguage']);
        unset($_SESSION['reviewLink']);
        header('Location:booked_users.php');
    }
    if(isset($_SESSION['mailText']) && isset($_SESSION['cancelEmail'])){
        $mail->addAddress($_SESSION['cancelEmail'],$_SESSION['cancelEmail']);
        $mail->addReplyTo('info@example.com', 'Information');
        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com');
        $mail->Subject = $_SESSION['cancelSubject'];
        $mail->Body = $_SESSION['mailText'];
        $mail->AltBody = $_SESSION['mailText'];
        unset($_SESSION['mailText']);
        unset($_SESSION['cancelMail']);
        unset($_SESSION['cancelSubject']);
        header('Location:booked_users.php');
    }
    if (isset($_SESSION['email']) && !empty($_SESSION['email']) && isset($_SESSION['verification_code']) && !isset($_SESSION["workerEmail"])) {

        $mail->addAddress($_SESSION['email'], $_SESSION['email']);
        $mail->addReplyTo('info@example.com', 'Information');
        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com');
        $mail->Subject = "R&D";
        $mail->Body = "<h2>Validate</h2> Your code:<br><h3>" . $_SESSION['verification_code'] . "</h3>";
        $mail->AltBody = "Your code:<br><h3>" . $_SESSION['verification_code'] . "</h3>";
        $_SESSION['message'] = "<b>Check your mail for account verification</b>";
        unset($_SESSION['verification_code']);
        header('Location:email-verification.php');
    }


    if (isset($_SESSION['mailReset']) && isset($_SESSION['resetCode'])) {
        $mail->addAddress($_SESSION['mailReset'], $_SESSION['mailReset']);
        $mail->addReplyTo('info@example.com', 'Information');
        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com');
        $mail->Subject = "R&D";
        $_SESSION['message'] = "<b>Check your mail for password verification</b>";
        $mail->Body = "<h2>Reset password</h2> Your code:<br><h3>" . $_SESSION['resetCode'] . "</h3>";
        $mail->AltBody = "Your code:<br><h3>" . $_SESSION['resetCode'] . "</h3>";
        unset($_SESSION['resetCode']);
        header('Location:email-verification.php');
    }
    if (isset($_SESSION["veterinarianEmail"])) {

        $mail->addAddress($_SESSION["workerEmail"], $_SESSION['name']);
        $mail->addReplyTo('info@example.com', 'Information');
        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com');
        $mail->Subject = "R&D";

        $_SESSION['message'] = "<b>Worker is added</b>";
        $mail->Body = "<h2>You are hired :)</h2> Set up your profile <a href=" . $_SESSION['workerLink'] . ">here</a>";
        $mail->AltBody = "<h2>You are hired :)</h2> Set up your profile <a href=" . $_SESSION['workerLink'] . ">here</a>";
        unset($_SESSION['workerEmail']);
        header('Location:addVet.php');
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
    exit();

} catch (Exception $e) {

    $_SESSION['message'] = "Message could not be sent. Mailer Error: " . $e->getMessage();
}