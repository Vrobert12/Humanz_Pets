<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include "config.php";
require 'phpqrcode/qrlib.php';
require "vendor/autoload.php";
$function = new Functions();

class Functions
{
    private $connection;

    public function __construct()
    {
        global $dsn, $pdoOptions;

        // Access the constant PARAMS directly
        $user = PARAMS['USER'];
        $password = PARAMS['PASSWORD'];

        $this->connection = $this->connect($dsn, $user, $password, $pdoOptions);


        if (!isset($_GET['email'])) {
            $this->run();
        }
    }

    // Connect to the database
    public function connect($dsn, $user, $password, $pdoOptions)
    {
        try {
            return new PDO($dsn, $user, $password, $pdoOptions);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }


    public function run()
    {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'registration':
                    $this->registration();
                    break;
                case 'Log in':
                    $this->login();
                    break;
                case 'kijelentkezes':
                    $this->logOut();
                    break;
                case 'picture':
                    $this->picture($_SESSION['backPic']);
                    break;
                case 'ResetPass':
                    $this->resetPassword();
                    break;
                case 'ModifyUser':
                    $this->modifyUser();
                    break;
                case 'Send':
                    $this->mailAddAndPasswordChange();
                    break;
                case 'AddWorker':
                    $this->addWorker();
                    break;
                case 'ModifyTable':
                    $this->modifyTable();
                    break;
                case 'AddMenu':
                    $this->addMenu();
                    break;
                case 'ModifyDish':
                    $this->modifyMenu();
                    break;
                case 'AddCoupon':
                    $this->addCoupon();
                    break;
                case 'BanPerson':
                case 'UnBanPerson':
                    $this->ban();
                    break;
                case 'AddTable':
                    $this->addTable();
                    break;
                case "registerAnimal":
                    $this->registerAnimal();
                    break;
                case "veterinarianChose":
                    $this->choseVeterinarian();
                    break;
                default:
                    $_SESSION['message'] = "Something went wrong in switch";
                    header('Location:index.php');
                    exit();
            }
        } elseif (isset($_GET['action'])) {
            if ($_GET['action'] === 'logOut') {
                $this->logOut();
            }
        }
    }

    /**
     * @param string $name
     * @param string $petName
     * @param string $bred
     * @param string $specie
     * @param string $petPicture
     * @param string $email
     * @param string $phone
     * @return string
     */
    public function createQrCode(string $name, string $phone): string
    {
        $saveDir = 'QRcodes/';
        if (!is_dir($saveDir)) {
            mkdir($saveDir, 0777, true);
        }

        $vCardData = "BEGIN:VCARD\r\n";
        $vCardData .= "VERSION:3.0\r\n";
        $vCardData .= "FN:$name\r\n";
        $vCardData .= "TEL:$phone\r\n";
        $vCardData .= "END:VCARD";

        $vCardData = iconv('ISO-8859-1', 'UTF-8//IGNORE', $vCardData);

        $fileName = 'qrcode_' . uniqid() . '.png';
        $filePath = $saveDir . $fileName;

        QRcode::png($vCardData, $filePath, QR_ECLEVEL_L, 4);

        return $filePath;
    }


    public function registerAnimal()
    {
        if (isset($_POST["petName"]) && !empty($_POST["petName"]) &&
            isset($_POST["bred"]) && !empty($_POST["bred"]) &&
            isset($_POST["specie"]) && !empty($_POST["specie"]) &&
            isset($_FILES["picture"]) && !empty($_FILES["picture"])) {

            try {

                // Check if the user already has an existing QR code
                $stmt = "SELECT qr_code_id, qrCodeName FROM qr_code 
                 WHERE userId = :userId";
                $query = $this->connection->prepare($stmt);
                $userId = $_SESSION['userId'];
                $query->bindParam(':userId', $userId, PDO::PARAM_INT);

                $qrCodeId = null;
                $qrCodeFileName = null;

                // Execute the query to check for an existing QR code
                if ($query->execute() && $query->rowCount() > 0) {
                    // Reuse the existing QR code
                    $result = $query->fetch(PDO::FETCH_ASSOC);
                    $qrCodeId = $result['qr_code_id'];
                    $qrCodeFileName = $result['qrCodeName'];
                } else {
                    // Generate a new QR code if one doesn't exist
                    $qrCodeFileName = $this->createQrCode($_SESSION['name'], $_SESSION["phone"]);

                    // Insert the new QR code into the database
                    $insertQrStmt = "INSERT INTO qr_code (qrCodeName,generated_at,userId) VALUES (:qrCodeFile,:generated_at,:userId)";
                    $insertQuery = $this->connection->prepare($insertQrStmt);
                    $insertQuery->bindParam(':qrCodeFile', $qrCodeFileName, PDO::PARAM_STR);
                    $date = date('Y-m-d H:i:s');
                    $insertQuery->bindParam(':generated_at', $date, PDO::PARAM_STR);
                    $insertQuery->bindParam(':userId', $userId, PDO::PARAM_INT);

                    if ($insertQuery->execute()) {
                        // Retrieve the new QR code ID
                        $qrCodeIdStmt = "SELECT qr_code_id FROM qr_code WHERE qrCodeName = :qrCodeFileName";
                        $qrCodeQuery = $this->connection->prepare($qrCodeIdStmt);
                        $qrCodeQuery->bindParam(':qrCodeFileName', $qrCodeFileName, PDO::PARAM_STR);
                        $qrCodeQuery->execute();
                        $qrCodeId = $qrCodeQuery->fetchColumn();
                    } else {
                        throw new Exception("Failed to insert the new QR code.");
                    }
                }

                // Prepare and sanitize the pet data
                $petName = ucfirst(strtolower(trim($_POST["petName"])));
                $bred = ucfirst(strtolower(trim($_POST["bred"])));
                $specie = ucfirst(strtolower(trim($_POST["specie"])));
                $_SESSION['petPicture'] = $this->picture($_SESSION['backPic']);

                // Insert the pet data into the database
                $petStmt = "INSERT INTO pet (petName, bred, petSpecies, petPicture, userId)
                    VALUES (:petName, :bred, :specie, :petPicture, :userId)";
                $petQuery = $this->connection->prepare($petStmt);
                $petQuery->bindParam(':petName', $petName, PDO::PARAM_STR);
                $petQuery->bindParam(':bred', $bred, PDO::PARAM_STR);
                $petQuery->bindParam(':specie', $specie, PDO::PARAM_STR);
                $petQuery->bindParam(':petPicture', $_SESSION['petPicture'], PDO::PARAM_STR);
                $petQuery->bindParam(':userId', $userId, PDO::PARAM_INT);

                if ($petQuery->execute()) {
                    $_SESSION['message'] = "You registered your animal successfully. Now <b>chose the veterinarian</b><br>
 that will examine your pet";
                    header("Location: selectVeterinarian.php");
                    exit();
                } else {
                    throw new Exception("Failed to register the pet.");
                }

            } catch (Exception $e) {
                $_SESSION['message'] = "Error: " . $e->getMessage();
                header("Location: registerAnimal.php");
                exit();
            }
        } else {
            $_SESSION['message'] = "Please fill in all the fields.";
            header("Location: registerAnimal.php");
            exit();
        }


    }

    public function passwordCheck($password, $password2, $location)
    {
        if ($password == '') {
            $_SESSION['message'] = "The <b>Password</b> is not filled out";
            header('Location: ' . $location);
            exit();
        }
        if ($password2 == '') {
            $_SESSION['message'] = "The <b>Confirmation Password</b> is not filled out";
            header('Location: ' . $location);
            exit();
        }
        if ($password != $password2) {
            $_SESSION['message'] = "The Passwords do not match";
            header('Location: ' . $location);
            exit();
        }
        if (!(preg_match("/[a-z]/", $password))) {
            $_SESSION['message'] = "The <b>Password</b> does not contain <b>Lower case</b>.";
            header('Location: ' . $location);
            exit();
        }
        if (!(preg_match("/[A-Z]/", $password))) {
            $_SESSION['message'] = "The <b>Password</b> does not contain <b>Upper case</b>.";
            header('Location: ' . $location);
            exit();
        }
        if (!(preg_match("/[0-9]+/", $password))) {
            $_SESSION['message'] = "The <b>Password</b> does not contain <b>Numbers</b>.";
            header('Location: ' . $location);
            exit();
        }

        if (strlen($password) < 8) {
            $_SESSION['message'] = "The <b>Password</b> has to be <b>8 characters long</b>.";
            header('Location: ' . $location);
            exit();
        }

    }

    public function userCheck1($fname, $lname, $email, $tel2, $location)
    {
        if ($fname == '') {
            $_SESSION['message'] = "The <b>First Name</b> is not filled out";
            header('Location: ' . $location);
            exit();
        }
        if ($lname == '') {
            $_SESSION['message'] = "The <b>Last Name</b> is not filled out";
            header('Location: ' . $location);
            exit();
        }
        if ($email == '') {
            $_SESSION['message'] = "The <b>E-mail</b> is not filled out";
            header('Location: ' . $location);
            exit();
        }
        if (preg_match("/[0-9]+/", $fname)) {
            $_SESSION['message'] = "The <b>First Name</b> filled contains <b>Numbers</b>.";
            header('Location: ' . $location);
            exit();
        }
        if (preg_match("/\s/", $fname)) {
            $_SESSION['message'] = "The <b>First Name</b> filled contains <b>Spaces</b>";
            header('Location: ' . $location);
            exit();
        }
        if (preg_match("/[0-9]+/", $lname)) {
            $_SESSION['message'] = "The <b>Last Name</b> filled contains <b>Numbers</b>";
            header('Location: ' . $location);
            exit();
        }
        if (preg_match("/\s/", $lname)) {
            $_SESSION['message'] = "The <b>Last Name</b> filled contains <b>Spaces</b>";
            header('Location: ' . $location);
            exit();
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['message'] = "The <b>E-mail</b> does not exist!";
            header('Location: ' . $location);
            exit();
        }
        if ($tel2 != "") {
            if (strlen($tel2) != 7) {
                $_SESSION['message'] = "The <b>Phone Number</b> does not exist!";
                header('Location: ' . $location);
                exit();
            }
        } else {
            $_SESSION['message'] = "The <b>Phone Number</b> is not filled out";
            header('Location: ' . $location);
            exit();
        }

    }

    public function registration()
    {
        if (isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['tel1']) && isset($_POST['tel2']) && isset($_POST['mail']) && isset($_POST['pass']) && isset($_POST['pass2'])) {


            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $tel1 = $_POST['tel1'];

            $tel2 = $_POST['tel2'];
            $tel = $tel1 . "" . $tel2;

            $mail = $_POST['mail'];
            $_SESSION["email"] = $mail;
            $pass = $_POST['pass'];
            $pass2 = $_POST['pass2'];

            $this->userCheck1($fname, $lname, $mail, $tel2, "registration.php");

            $this->passwordCheck($pass, $pass2, "registration.php");

            try {
                // SMTP settings
                $time = time();
                $verifyTime = time() + 60 * 10;
                $verification_time = date("Y-m-d H:i:s", $verifyTime);
                $currentTime = date("Y-m-d H:i:s", $time);

                $sql = "SELECT userMail,verify,verification_time FROM user";
                $stmtTeszt = $this->connection->query($sql);

                if ($stmtTeszt->rowCount() > 0) {
                    while ($rows = $stmtTeszt->fetch(PDO::FETCH_ASSOC)) {


                        if ($rows['userMail'] == $mail) {
                            if ($rows['verify'] == 1) {
                                $_SESSION['message'] = "The <b>Registration</b> has not been successful! Try again or check if your mail is not registered here";
                                header('Location: registration.php');
                                exit();
                            } else {

                                $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
                                $query = "UPDATE user SET verification_code = ? ,verification_time =? WHERE userMail = ?";
                                $query = $this->connection->prepare($query);
                                $query->execute([$verification_code, $verification_time, $mail]);
                                $_SESSION['message'] = "If you think the<b>E-mail</b> address is registered try again.";
                                $_SESSION['verification_code'] = $verification_code;
                                $_SESSION['email'] = $mail;

                                header('Location: mail.php');

                                exit();
                            }

                        }
                    }
                }
                $sql = "SELECT veterinarianMail,verify,verification_time FROM veterinarian";
                $stmtTeszt = $this->connection->query($sql);

                if ($stmtTeszt->rowCount() > 0) {
                    while ($rows = $stmtTeszt->fetch(PDO::FETCH_ASSOC)) {


                        if ($rows['userMail'] == $mail) {
                            if ($rows['verify'] == 1) {
                                $_SESSION['message'] = "The <b>Registration</b> has not been successful! Try again or check if your mail is not registered here";
                                header('Location: registration.php');
                                exit();
                            } else {

                                $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
                                $query = mysqli_prepare($this->connection, "UPDATE veterinarian SET verification_code = ? ,verification_time =? WHERE veterinarianMail = ?");
                                $query->bind_param("sss", $verification_code, $verification_time, $mail);
                                $query->execute();
                                $_SESSION['message'] = "If you think the<b>E-mail</b> address is registered try again.";
                                $_SESSION['verification_code'] = $verification_code;
                                $_SESSION['email'] = $mail;

                                header('Location: mail.php');

                                exit();
                            }

                        }
                    }
                }
                $kep = "logInPic.png";


                // Send email

                // Hash the password
                $pass = password_hash($pass, PASSWORD_BCRYPT);
                $verification_code = substr(number_format(time() * rand(), 0, '',
                    ''), 0, 7);
                $banned = 0;
                $privilage = "Guest";
                if ($_POST['rank'])
                    $privilage = "Worker";
                $banned_time = null;
                $verification_code_expire = null;
                $verification_code_pass = null;

                // Insert user data into the database
                $sql = "INSERT INTO user (firstName, lastName, phoneNumber, userMail, userPassword,
                  verification_code, verify, profilePic,
                  privilage, registrationTime, verification_time, banned, banned_time, passwordValidation, passwordValidationTime) 
        VALUES (:firstName, :lastName, :phoneNumber, :userMail, :userPassword, 
                :verification_code, :verify, :profilePic, :privilage, :registrationTime, 
                :verification_time, :banned, :banned_time, :passwordValidation, :passwordValidationTime)";

                $stmt = $this->connection->prepare($sql);

                $verification = 0; // Placeholder for verify column

                $stmt->bindParam(':firstName', $fname, PDO::PARAM_STR);
                $stmt->bindParam(':lastName', $lname, PDO::PARAM_STR);
                $stmt->bindParam(':phoneNumber', $tel, PDO::PARAM_STR);
                $stmt->bindParam(':userMail', $mail, PDO::PARAM_STR);
                $stmt->bindParam(':userPassword', $pass, PDO::PARAM_STR);
                $stmt->bindParam(':verification_code', $verification_code, PDO::PARAM_INT);
                $stmt->bindParam(':verify', $verification, PDO::PARAM_INT);
                $stmt->bindParam(':profilePic', $kep, PDO::PARAM_STR);
                $stmt->bindParam(':privilage', $privilage, PDO::PARAM_STR);
                $stmt->bindParam(':registrationTime', $currentTime, PDO::PARAM_STR);
                $stmt->bindParam(':verification_time', $verification_time, PDO::PARAM_STR);
                $stmt->bindParam(':banned', $banned, PDO::PARAM_INT);
                $stmt->bindParam(':banned_time', $banned_time, PDO::PARAM_STR);
                $stmt->bindParam(':passwordValidation', $verification_code_pass, PDO::PARAM_STR);
                $stmt->bindParam(':passwordValidationTime', $verification_code_expire, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $_SESSION['message'] = "We sent an email to you!";
                    $_SESSION['text'] = "<h2>Registration</h2>";
                    $_SESSION['verification_code'] = $verification_code;
                    $_SESSION['mail'] = $mail;
                    header('Location: mail.php');
                    exit(); // Exit script after redirection
                } else {
                    $_SESSION['message'] = "Error occurred during registration: " . $this->connection->error;
                    header('Location: registration.php');
                    exit();
                }


            } catch (Exception $e) {
                $_SESSION['message'] = "An error occurred: " . $e->getMessage();
            }
        } else {
            $_SESSION['message'] = "Error occurred during registration!";
        }

    }

    public function userModifyData($fname, $lname, $tel2, $location)
    {

        if (preg_match("/[0-9]+/", $fname)) {
            $_SESSION['message'] = "The <b>First Name</b> filled contains <b>Numbers</b>.";
            header('Location: ' . $location);
            exit();
        }
        if (preg_match("/\s/", $fname)) {
            $_SESSION['message'] = "The <b>First Name</b> filled contains <b>Spaces</b>";
            header('Location: ' . $location);
            exit();
        }
        if (preg_match("/[0-9]+/", $lname)) {
            $_SESSION['message'] = "The <b>Last Name</b> filled contains <b>Numbers</b>";
            header('Location: ' . $location);
            exit();
        }
        if (preg_match("/\s/", $lname)) {
            $_SESSION['message'] = "The <b>Last Name</b> filled contains <b>Spaces</b>";
            header('Location: ' . $location);
            exit();
        }
        if ($tel2 != "") {
            if (strlen($tel2) != 7) {
                $_SESSION['message'] = "The <b>Phone Number</b> does not exist!";
                header('Location: ' . $location);
                exit();
            }
        }


    }

    public function modifyUser()
    {
        $count = 0;
        $phoneNumber = $_POST['tel1'] . $_POST['tel2'];

        // Prepare the initial query to retrieve existing user details
        $sql = $this->connection->prepare("SELECT firstName, lastName, phoneNumber FROM user WHERE userMail = ?");
        $sql->execute([$_SESSION['email']]);
        $result = $sql->fetch(PDO::FETCH_ASSOC);

        // Calling userModifyData function with posted data
        $this->userModifyData($_POST['firstName'], $_POST['lastName'], $_POST['tel2'], "modify.php");

        // Check if user data was found and update if necessary
        if ($result) {
            // Update first name if provided
            if (!empty($_POST['firstName'])) {
                $sql = $this->connection->prepare("UPDATE user SET firstName = ? WHERE userMail = ?");
                $sql->execute([$_POST['firstName'], $_SESSION['email']]);
                $_SESSION['name'] = $_POST['firstName'];
                $_SESSION['message'] = "First name is modified";
                $count++;
            } else {
                $_SESSION['name'] = $result['firstName'];
            }

            // Update last name if provided
            if (!empty($_POST['lastName'])) {
                $sql = $this->connection->prepare("UPDATE user SET lastName = ? WHERE userMail = ?");
                $sql->execute([$_POST['lastName'], $_SESSION['email']]);
                $_SESSION['name'] .= " " . $_POST['lastName'];
                $_SESSION['message'] = "Last name is modified";
                $count++;
            } else {
                $_SESSION['name'] .= " " . $result['lastName'];
            }

            // Update phone number if provided
            if (!empty($_POST['tel1']) && !empty($_POST['tel2'])) {
                $sql = $this->connection->prepare("UPDATE user SET phoneNumber = ? WHERE userMail = ?");
                $sql->execute([$phoneNumber, $_SESSION['email']]);
                $_SESSION['message'] = "Phone number is modified";
                $count++;
            }
        }

        // Set session message based on whether any changes were made
        $_SESSION['message'] = $count > 0 ? "We made changes to your profile" : "There are no changes made to your profile";
        $stmt = "Select firstName,lastName,phoneNumber from user WHERE userId = :userId";
        $stmt = $this->connection->prepare($stmt);
        $stmt->bindParam(':userId', $_SESSION['userId']);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $_SESSION['qrCodeFile'] = $this->createQrCode($row['firstName'] . ' ' . $row['lastName'], $row['phoneNumber']);

        }
        $stmt = "UPDATE qr_code qr
INNER JOIN pet p ON qr.qr_code_id = p.qr_code_id
SET qr.qrCodeName = :qrCodeName
WHERE p.userId = :userId;
";
        $stmt = $this->connection->prepare($stmt);
        $stmt->bindParam(':userId', $_SESSION['userId']);
        $stmt->bindParam(':qrCodeName', $_SESSION['qrCodeFile']);
        $stmt->execute();


        // Redirect to index.php
        header('Location: index.php');
        exit();
    }

    public function picture($target = " ")
    {

        if (isset($_FILES['picture'])) {
            $target_dir = "pictures/";  // Local directory for storing uploaded files
            $target_file = $target_dir . basename($_FILES["picture"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $kep = pathinfo($target_file, PATHINFO_FILENAME);
            $kep_dir = $imageFileType;
            $kep = $kep . "." . $kep_dir;

            if ($_FILES['picture']["error"] > 0) {
                $_SESSION['message'] = $_FILES["picture"]["error"];
                return $_SESSION['message'];
            } else {
                if (is_uploaded_file($_FILES['picture']['tmp_name'])) {

                    $file_name = $_FILES['picture']["name"];
                    $file_temp = $_FILES["picture"]["tmp_name"];
                    $file_size = $_FILES["picture"]["size"];
                    $file_type = $_FILES["picture"]["type"];
                    $file_error = $_FILES['picture']["error"];

                    if (!exif_imagetype($file_temp)) {
                        $_SESSION['message'] = "File is not a picture!";
                        $logType = "Picture";
                        $logText = "The file is not in correct format";
                        $logMessage = $_SESSION['message'];

                        $this->errorLogInsert($_SESSION['email'], $logText, $logType, $logMessage);
                        header('location: ' . $target);
                        exit();
                    }
                    $file_size = $file_size / 1024;
                    if ($file_size > 300) {
                        $_SESSION['message'] = "File is too big!";
                        $logType = "Picture";
                        $logText = "The file is bigger than 300KB";
                        $logMessage = $_SESSION['message'];

                        $this->errorLogInsert($_SESSION['email'], $logText, $logType, $logMessage);
                        header('location: ' . $target);
                        exit();
                    }

                    $ext_temp = explode(".", $file_name);
                    $extension = end($ext_temp);

                    if (isset($_POST['alias'])) {
                        $alias = $_POST['alias'];
                    } else {
                        $alias = "";
                    }

                    $new_file_name = Date("YmdHis") . "$alias.$extension";
                    $upload = "$target_dir$new_file_name";

                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true); // Create the directory if it doesn't exist
                    }

                    if (!file_exists($upload)) {
                        if (move_uploaded_file($file_temp, $upload)) {
                            $size = getimagesize($upload);
                            var_dump($size);
                            foreach ($size as $key => $value)
                                echo "$key = $value<br>";

                            echo "<img src=\"$upload\" $size[3] alt=\"$file_name\">";
                        } else {
                            echo "<p><b>Error!</b> Failed to move uploaded file.</p>";
                        }
                    } else {
                        echo "<p><b>Error!</b> File with this name already exists!</p>";
                    }
                } else {
                    echo "<p><b>Error!</b> Possible file upload attack!</p>";
                }

                if ($target != "addTable.php" && $target != "modifyMenu.php" && $target != "modifyTable.php" && $target != "addMenu.php") {
                    if ($target == "index.php" || $target == "users.php" || $target == "workers.php" || $target == "tables.php"
                        || $target == "reports.php" || $target == "menu.php" || $target == "coupon.php") {

                        $query = $this->connection->prepare("UPDATE user SET profilePic = :profilePic WHERE userMail = :userMail");
                        $query->bindValue(":profilePic", $new_file_name, PDO::PARAM_STR);
                        $query->bindValue(":userMail", $_SESSION['email'], PDO::PARAM_STR);
                        $query->execute();

                        $_SESSION['profilePic'] = $new_file_name;
                        // Redirect to login page after successful upload
                        header('Location: ' . $_SESSION['backPic']);
                        exit(); // Exit after redirection
                    } elseif ($target == 'registerAnimal.php') {
                        return $new_file_name;
                    } else {
                        $mail = 'Unknown';
                        $logType = "file Upload";
                        $logText = "Someone tried to upload a picture from a not valid page";
                        $logMessage = "You can't upload a picture from another page!";

                        $this->errorLogInsert($mail, $logText, $logType, $logMessage);
                        $_SESSION['message'] = "You can't upload a picture from another page!";
                        header('Location: ' . $_SESSION['backPic']);
                        exit();
                    }
                }
            }
        } else {
            $_SESSION['message'] = "File not found!";
        }
        return $new_file_name;
    }

    public function logOut()
    {
        $_SESSION = [];
        session_unset();
        session_destroy();
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach ($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time() - 3600, '/');
                unset($_COOKIE[$name]);
            }
        }

        // Redirect to login page
        header('Location: index.php');
        exit();
    }

    public function login()
    {
        if (isset($_POST['mail'], $_POST['pass'])) {
            echo 'Login triggered<br>';
            $mail = $_POST['mail'];
            $password = $_POST['pass'];

            // Check if values are captured
            echo "Email: $mail, Password: $password<br>";
        }
        if (isset($_POST['mail'], $_POST['pass'])) {
            $mail = $_POST['mail'];
            $password = $_POST['pass'];

            $sql = "SELECT * FROM user WHERE userMail = :mail";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(":mail", $mail);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                sleep(2);
                if (empty($result['userPassword'])) {
                    $_SESSION['message'] = "The worker did not set up a password!";
                } else {
                    if (password_verify($password, $result['userPassword'])) {
                        if ($result['banned']) {
                            $_SESSION['message'] = "You have been banned from our website!";
                        } else {
                            $_SESSION['email'] = $result['userMail'];
                            $_SESSION['name'] = $result['firstName'] . " " . $result['lastName'];
                            $_SESSION['profilePic'] = $result['profilePic'];
                            $_SESSION['userId'] = $result['userId'];
                            $_SESSION['phone'] = $result['phoneNumber'];
                            $_SESSION['privilage'] = $result['privilage'];

                            setcookie("email", $result['userMail'], time() + time() + 10 * 60, "/");
                            setcookie("name", $result['firstName'] . " " . $result['lastName'], time() + time() + 10 * 60, "/");
                            setcookie("profilePic", $result['profilePic'], time() + time() + 10 * 60, "/");
                            setcookie("userId", $result['userId'], time() + time() + 10 * 60, "/");
                            setcookie("phone", $result['phoneNumber'], time() + time() + 10 * 60, "/");
                            setcookie("privilage", $result['privilage'], time() + time() + 10 * 60, "/");

                            setcookie("last_activity", time(), time() + 10 * 60, "/");
                            header('Location: index.php');
                            exit();
                        }
                    } else {
                        $this->errorLogInsert($mail, "The password was not valid!", "Log in", "Wrong password!");
                        $_SESSION['message'] = "Wrong password!";
                    }
                }
            } else {
                $_SESSION['message'] = "Something went wrong, maybe the mail is not registered!";
                $this->errorLogInsert($mail, "The E-mail is not in our database", "Log in", $_SESSION['message']);
            }
        } else {
            $_SESSION['message'] = "Email or password not set!";
        }

        header('Location: logIn.php');
        exit();
    }

    public function checkAutoLogin(string $currentPage = null)
    {
        if (!isset($_GET['action'])) {
            if (isset($_COOKIE['last_activity']) && isset($_SESSION['email'])) {

                $mail = $_SESSION['email'];
                $sql = "select petId from pet where userId=:userId and veterinarId is NULL";
                $stmt = $this->connection->prepare($sql);
                $stmt->bindParam(":userId", $_SESSION["userId"]);
                $stmt->execute();
                if ($stmt->rowCount() == 1) {
                    $_SESSION['message'] = 'You have to choose a veterinarian for your animal,<br> before you can go further<br><br><a href="functions.php?action=logOut">Log out</a> ';
                    if ($currentPage != 'selectVeterinarian.php') {
                        header("Location:selectVeterinarian.php");
                        exit();
                    }
                }


                unset($_COOKIE['last_activity']);
                unset($_COOKIE['email']);
                setcookie("email", $mail, time() + time() + 10 * 60, "/");
                setcookie("last_activity", time(), time() + 10 * 60, "/");
                $sql = "SELECT p.petId FROM  pet p  inner join user u   on p.userId=u.userId  where u.userMail = :mail";
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(":mail", $mail);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result == 0) {
                    $_SESSION['message'] = '<br>You need to register an animal, without it you <b>can not</b> use the account.<br><br><a href="functions.php?action=logOut">Log out</a>';
                    if ($currentPage != 'registerAnimal.php') {
                        header('Location: registerAnimal.php');
                        exit();
                    }
                }
            } elseif (isset($_COOKIE['email'])) {
                $_SESSION['email'] = $_COOKIE['email'];
                $_SESSION['name'] = $_COOKIE['name'];
                $_SESSION['profilePic'] = $_COOKIE['profilePic'];
                $_SESSION['userId'] = $_COOKIE['userId'];
                $_SESSION['phone'] = $_COOKIE['phone'];
                $_SESSION['privilage'] = $_COOKIE['privilage'];
                $mail = $_SESSION['email'];
                $sql = "select petId from pet where userId=:userId and veterinarId is NULL";
                $stmt = $this->connection->prepare($sql);
                $stmt->bindParam(":userId", $_SESSION["userId"]);
                $stmt->execute();
                if ($stmt->rowCount() == 1) {
                    $_SESSION['message'] = 'You have to choose a veterinarian for your animal,<br> before you can go further<br><br><a href="functions.php?action=logOut">Log out</a> ';
                    if ($currentPage != 'selectVeterinarian.php') {
                        header("Location:selectVeterinarian.php");
                        exit();
                    }
                }

                unset($_COOKIE['last_activity']);

                unset($_COOKIE['email']);
                unset($_COOKIE['name']);
                unset($_COOKIE['profilePic']);
                unset($_COOKIE['userId']);
                unset($_COOKIE['phone']);
                unset($_COOKIE['privilage']);

                setcookie("email", $_SESSION['email'], time() + time() + 10 * 60, "/");
                setcookie("name", $_SESSION['name'], time() + time() + 10 * 60, "/");
                setcookie("profilePic", $_SESSION['profilePic'], time() + time() + 10 * 60, "/");
                setcookie("userId", $_SESSION['userId'], time() + time() + 10 * 60, "/");
                setcookie("phone", $_SESSION['phone'], time() + time() + 10 * 60, "/");
                setcookie("privilage", $_SESSION['privilage'], time() + time() + 10 * 60, "/");

                setcookie("last_activity", time(), time() + 10 * 60, "/");
                $sql = "SELECT p.petId FROM  pet p  inner join user u   on p.userId=u.userId  where u.userMail = :mail";
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(":mail", $mail);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result == 0) {
                    $_SESSION['message'] = '<br>You need to register an animal, without it you <b>can not</b> use the account.<br><br><a href="functions.php?action=logOut">Log out</a>';
                    if ($currentPage != 'registerAnimal.php') {
                        header('Location: registerAnimal.php');
                        exit();
                    }

                } else {
                    $sql = "SELECT v.veterinarianID FROM veterinarian v inner join pet p on
 p.veterinarId=v.veterinarianID inner join user u on u.userId=p.userId where userMail=:mail";
                    $stmt = $this->connection->prepare($sql);
                    $stmt->bindValue(":mail", $mail);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($result == 0 && $_SESSION['privilage'] != 'admin') {
                        $_SESSION['message'] = '<br>You have to <b>chose a veterinarian</b> to use this account further!<br><br><a href="functions.php?action=logOut">Log out</a>';
                        if ($currentPage != 'selectVeterinarian.php' && $currentPage != 'registerAnimal.php') {
                            header('Location: selectVeterinarian.php');
                            exit();
                        }
                    }
                }
                header('Location: index.php');
                exit();

            } elseif (isset($_SESSION['email'])) {
                session_destroy();
                header('Location: logIn.php');
                exit();
                // No valid cookies or session
            }
        }
    }

    private function errorLogInsert($mail, $errorText, $logType, $logMessage)
    {
        sleep(2);
        $currentTime = date("Y-m-d H:i:s");

        $sql = "INSERT INTO errorlog (errorType, errorMail, errorText, errorTime)
                VALUES (:errorType, :errorMail, :errorText, :errorTime)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':errorType', $logType);
        $stmt->bindValue(':errorMail', $mail);
        $stmt->bindValue(':errorText', $errorText);
        $stmt->bindValue(':errorTime', $currentTime);

        if ($stmt->execute()) {
            $_SESSION['message'] = $logMessage;
        } else {
            $_SESSION['message'] = "Failed to log error.";
        }
    }

    private function choseVeterinarian()
    {
        $sql = "UPDATE pet SET veterinarId=:veterinarianId WHERE petId=:petId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':veterinarianId', $_SESSION['veterinarianId']);
        $stmt->bindValue(':petId', $_SESSION['petId']);
        $stmt->execute();
        header('Location: index.php');
        exit();
    }
}
