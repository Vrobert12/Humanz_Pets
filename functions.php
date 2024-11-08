<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
include "config.php";
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

        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
        }

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
    public function userModifyData($fname, $lname, $tel2, $location) {

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
    public function modifyUser() {
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

        // Redirect to index.php
        header('Location: index.php');
        exit();
    }

    public function picture($target = " ") {

        if (isset($_FILES['picture'])) {
            $target_dir = "pictures/";  // Local directory for storing uploaded files
            $target_file = $target_dir . basename($_FILES["picture"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $kep = pathinfo($target_file, PATHINFO_FILENAME);
            $kep_dir = $imageFileType;
            $kep = $kep . "." . $kep_dir;

            if ($_FILES['picture']["error"] > 0) {
                return $_FILES["picture"]["error"];
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
                    if ($file_size > 200) {
                        $_SESSION['message'] = "File is too big!";
                        $logType = "Picture";
                        $logText = "The file is bigger than 200KB";
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
        }
        return $new_file_name;
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
