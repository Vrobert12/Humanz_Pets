<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    session_start();
    //Server settings
    $_SESSION['message']="";
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;

    $mail->isSMTP();
    $mail->Host = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth = true;
    $mail->Port = 2525;
    $mail->Username = 'f96cd0b03680d3';
    $mail->Password = 'e5afda4b929822';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('humanz@humanz.stud.vts.su.ac.rs', 'Mailer');

if(isset($_SESSION['email'])&& !empty($_SESSION['email']) && isset($_SESSION['verification_code']) && !isset($_SESSION["workerEmail"])) {

    $mail->addAddress($_SESSION['email'], 'Varró Róbert');
    $mail->addReplyTo('info@example.com', 'Information');
    $mail->addCC('cc@example.com');
    $mail->addBCC('bcc@example.com');
    $mail->Subject = "R&D";
    $mail->Body = "<h2>Validate</h2> Your code:<br><h3>" . $_SESSION['verification_code'] . "</h3>";
    $mail->AltBody = "Your code:<br><h3>" . $_SESSION['verification_code'] . "</h3>";
    $_SESSION['message']="<b>Check your mail for account verification</b>";
    unset($_SESSION['verification_code']);
    header('Location:email-verification.php');
}


    if(isset($_SESSION['mailReset']) && isset($_SESSION['resetCode'])){
        $mail->addAddress($_SESSION['mailReset'], 'Varró Róbert');
        $mail->addReplyTo('info@example.com', 'Information');
        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com');
        $mail->Subject = "R&D";
$_SESSION['email']=$_SESSION['mailReset'];
        $_SESSION['message']="<b>Check your mail for password verification</b>";
        $mail->Body = "<h2>Reset password</h2> Your code:<br><h3>".$_SESSION['resetCode']."</h3>";
        $mail->AltBody = "Your code:<br><h3>".$_SESSION['resetCode']."</h3>";
        unset($_SESSION['resetCode']);
        header('Location:email-verification.php');
    }
    if( isset($_SESSION["workerEmail"])){

        $mail->addAddress($_SESSION["workerEmail"], $_SESSION['name']);
        $mail->addReplyTo('info@example.com', 'Information');
        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com');
        $mail->Subject = "R&D";

        $_SESSION['message']="<b>Worker is added</b>";
        $mail->Body = "<h2>You are hired :)</h2> Set up your profile <a href=".$_SESSION['workerLink'].">here</a>";
        $mail->AltBody = "<h2>You are hired :)</h2> Set up your profile <a href=".$_SESSION['workerLink'].">here</a>";
        unset($_SESSION['workerEmail']);
        header('Location:workers.php');
    }
    if( isset($_SESSION["reservation"])){

        $mail->addAddress($_SESSION["email"], $_SESSION['name']);
        $mail->addReplyTo('info@example.com', 'Information');
        $mail->addCC('cc@example.com');
        $mail->addBCC('bcc@example.com');
        $mail->Subject = "R&D";

        $_SESSION['message']="<b>You have reserved table ". $_SESSION['reservationTable']."</b>";
        $mail->Body = "<h2>You have reserved our table </h2>  Reservation is on day <b>".$_SESSION['day']."</b> from <b>"
            .$_SESSION['reservationTime']."</b> to <b>".$_SESSION['reservationTimeEnd'].". Your reservation code is: ".$_SESSION['reservationCode']."</b>";
        $mail->AltBody = "<h2>You have reserved our table </h2>  Reservation is on day <b>".$_SESSION['day']."</b> from <b>"
            .$_SESSION['reservationTime']."</b> to <b>".$_SESSION['reservationTimeEnd'].". Your reservation code is: ".$_SESSION['reservationCode']."</b>";
        unset($_SESSION['workerEmail']);
        unset($_SESSION['reservationCode']);
        header('Location:reservation.php?table='.$_SESSION['reservationTable']);
    }
    $mail->send();
    if(isset($_POST['mail']))
    $_POST['mail']=$_SESSION['email'];
    exit();

} catch (Exception $e) {

    $_SESSION['message'] =  "Message could not be sent. Mailer Error: ".$e->getMessage();
}