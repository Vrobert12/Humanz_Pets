<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
$_SESSION['message'] = "Error occurred during registration!";
$function=new Functions();

class Functions
{
    private $connection;

    public function __construct()
    {
        include "config.php";

        $this->connection = $this->connect($dsn, $pdoOptions);
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
        }
        $this->run();
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
                default:
                    $_SESSION['message'] = "Something went wrong in switch";
                    header('Location:index.php');
                    exit();
            }
        } elseif (isset($_SESSION['action'])) {
            if ($_SESSION['action'] === 'kijelentkezes') {
                $this->logOut();
            }
        }
    }

    private function connect($dsn, $pdoOptions)
    {
        try {
            $pdo = new PDO($dsn, PARAMS['USER'], PARAMS['PASSWORD'], $pdoOptions);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
        return $pdo;
    }
    public function passwordCheck($password, $password2, $location) {
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

    public function userCheck1($fname, $lname, $email, $tel2, $location) {
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
    public function registration() {
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

                if ($stmtTeszt->rowCount()> 0) {
                    while ($rows = $stmtTeszt->fetch(PDO::FETCH_ASSOC)) {


                        if ($rows['userMail'] == $mail) {
                            if ($rows['verify'] == 1) {
                                $_SESSION['message'] = "The <b>Registration</b> has not been successful! Try again or check if your mail is not registered here";
                                header('Location: registration.php');
                                exit();
                            } else {

                                $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
                                $query = mysqli_prepare($this->connection, "UPDATE user SET verification_code = ? ,verification_time =? WHERE userMail = ?");
                                $query->bind_param("sss", $verification_code, $verification_time, $mail);
                                $query->execute();
                                $_SESSION['message'] = "If ypu think the<b>E-mail</b> address is registered try again.";
                                $_SESSION['verification_code'] = $verification_code;
                                $_SESSION['email'] = $mail;

                                header('Location: mail.php');

                                exit();
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
                  verification_code,verify,profilePic,
                 privilage,registrationTime	,verification_time,banned,banned_time,passwordValidation,passwordValidationTime) 
VALUES (?,?,?,?, ?,? ,?, ?,?, ?,?,?,?,?,?)";
                    $stmt = $this->connection->prepare($sql);
                    $verrification = 0; // Placeholder for verification_code
                    $stmt->bind_param("ssissiissssisss", $fname, $lname, $tel, $mail, $pass,
                        $verification_code,
                        $verrification, $kep, $privilage, $currentTime, $verification_time,
                        $banned, $banned_time, $verification_code_pass, $verification_code_expire);

                    if ($stmt->execute()) {
                        $_SESSION['message'] = "We sent an email to you!";
                        $_SESSION['text'] = "<h2>Registration</h2>";
                        $_SESSION['verification_code'] = $verification_code;
                        $_SESSION['mail'] = $mail;
                        header('Location: mail.php');
                        exit(); // Exit script after redirection
                    } else {
                        $_SESSION['message'] = "Error occurred during registration: " .$this->connection->error;
                        header('Location: registration.php');
                        exit();
                    }

                }
            } catch (Exception $e) {
                $_SESSION['message'] = "An error occurred: " . $e->getMessage();
            }
        }
        else{
            $_SESSION['message'] = "Error occurred during registration!";
        }

    }
    public function logOut()
    {
        $_SESSION = [];
        session_unset();
        session_destroy();

        // Redirect to login page
        header('Location: index.php');
        exit();
    }

    public function login()
    { if (isset($_POST['mail'], $_POST['pass'])) {
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
                            $_SESSION['userID'] = $result['userId'];
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
}
