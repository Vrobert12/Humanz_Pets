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

        $this->connection = $this->connect($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $pdoOptions);


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
                case 'resetPass':
                    $this->resetPassword();
                    break;
                case 'ModifyUser':
                    $this->modifyUser();
                    break;
                case 'Send':
                    $this->mailAddAndPasswordChange();
                    break;
                case 'AddVet':
                    $this->addVet();
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
                case 'AddTable':
                    $this->addTable();
                    break;
                case "registerAnimal":
                    $this->registerAnimal();
                    break;
                case "addProduct":
                    $this->addProduct();
                    break;
                case "veterinarianChose":
                    $this->choseVeterinarian();
                    break;
                case "buyProduct":
                    $this->buyProduct();
                    break;
                case "updateProduct":
                    $this->updateProduct();
                    break;
                case "updatePet":
                    $this->updatePet();
                    break;
                case "deletePet":
                    $this->deletePet();
                    break;
                case "deleteReservation":
                    $this->deleteReservation();
                    break;
                case "insertReservation":
                    $this->insertReservation();
                    break;
                case "animalChecked":
                    $this->sendReview();
                    break;
                case "rateVeterinarian":
                    $this->insertReview();
                    break;
                case "deleteFromCart":
                    $this->deleteFromCart();
                    break;
                case "deleteFromProduct":
                    $this->deleteFromProduct();
                    break;
                case 'ban':
                    $this->ban();
                    break;
                case 'vetBan':
                    $this->vetBan();
                    break;
                case 'deleteReservationByVet':
                    $this->deleteReservationByVet();
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
    public function deleteReservationByVet()
    {
        if(!empty($_POST['mailText'])) {
            $sql = 'DELETE FROM reservation
WHERE reservationId = :reservationId
  AND (
      reservationDay > CURDATE() 
      OR (reservationDay = CURDATE() AND reservationTime > ADDTIME(NOW(), "01:00:00"))
  );';
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':reservationId', $_POST['reservationId']);
            $stmt->execute();
            $result = $stmt->rowCount();
            if ($result) {
                $_SESSION['message'] = "Reservation successfully deleted.";
                $_SESSION['mailText'] = $_POST['mailText'];
                $_SESSION['cancelEmail']= $_POST['cancelEmail'];

                if( $_POST['ownerLanguage']=="hu")
                $_SESSION['cancelSubject']= "Vizsgálat lemondva";

                if( $_POST['ownerLanguage']=="sr")
                    $_SESSION['cancelSubject']= "Pregled Odkazano";

                if( $_POST['ownerLanguage']=="en")
                    $_SESSION['cancelSubject']= "Reservation Cancelled";

                header('Location:mail.php');
                exit();
            } else
                $_SESSION['message'] = "Failed to delete the reservation. Please try again.";
            header('Location:booked_users.php');
            exit();
        }
        else
            $_SESSION['message'] = "Fill the message out.";
        header('Location:booked_users.php');
        exit();
    }

    public function vetBan()
    {
        if (isset($_POST['ban'])) {
            try {
                $time = time();
                $currentTime = date("Y-m-d H:i:s", $time);
                if ($_POST['ban'] == "yes") {
                    $_SESSION['message'] = "The person is unbanned:" .$_POST['veterinarianId'];
                    $sql = "UPDATE veterinarian SET banned=0 WHERE veterinarianId=:veterinarianId";

                } else {
                    $_SESSION['message'] = "The person is banned:" .$_POST['veterinarianId'];
                    $sql = "UPDATE veterinarian SET banned=1 WHERE veterinarianId=:veterinarianId";

                }
                $stmt = $this->connection->prepare($sql);
                $stmt->bindParam(':veterinarianId', $_POST['veterinarianId']);
                $stmt->execute();

                header('Location:' . $_SESSION['previousPage']);
                $_SESSION['previousPage'] = "";
                exit();
            } catch (Exception $e) {
                $_SESSION['message'] = "Something went wrong";
                header('Location:' . $_SESSION['previousPage']);
                $_SESSION['previousPage'] = "";
                exit();
            }
        }
    }
    public function ban()
    {
        if (isset($_POST['ban'])) {
            try {
                $time = time();
                $currentTime = date("Y-m-d H:i:s", $time);
                if ($_POST['ban'] == "yes") {
                    $_SESSION['message'] = "The person is unbanned: ".$_POST['userId'];
                    $sql = "UPDATE user SET banned=0 WHERE userId=:userId";

                } else {
                    $_SESSION['message'] = "The person is banned: ".$_POST['userId'];
                    $sql = "UPDATE user SET banned=1 WHERE userId=:userId";

                }
                $stmt = $this->connection->prepare($sql);
                $stmt->bindParam(':userId', $_POST['userId']);
                $stmt->execute();

                header('Location:' . $_SESSION['previousPage']);
                $_SESSION['previousPage'] = "";
                exit();
            } catch (Exception $e) {
                $_SESSION['message'] = "Something went wrong";
                header('Location:' . $_SESSION['previousPage']);
                $_SESSION['previousPage'] = "";
                exit();
            }
        }
    }

    public function deleteFromProduct()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $productId = $_POST["productId"];

            $sql = "Update user_product_relation set productId=NULL WHERE productId=:productId";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(":productId", $productId);
            $stmt->execute();


            $sql = "Delete from product WHERE productId=:productId";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(":productId", $productId );
            $stmt->execute();
            header('Location:products.php');
            exit();

        }
    }
    public function deleteFromCart()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $cartId = $_POST["cartId"];

            $sql = "Delete from user_product_relation WHERE userProductRelationId=:userProductRelationId";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(":userProductRelationId", $cartId);
            $stmt->execute();
            header('Location:products.php');
            exit();

        }
    }

    public function insertReview()
    {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $reviewCode = $_POST["reviewCode"];
            $rating = $_POST["rating"];

            $sql = "UPDATE review SET review=:review  where reviewCode=:reviewCode";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':reviewCode', $reviewCode);
            $stmt->bindParam(':review', $rating);
            $stmt->execute();
            header('Location:index.php');
            exit();

        }

    }

    public function sendReview()
    {
        $_SESSION['message'] = "Thank you for your feedback";
        $sql = "Update reservation set animalChecked=true where reservationId=:reservationId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':reservationId', $_POST['reservationId']);
        $stmt->execute();

        $reviewCode = $this->generateVerificationCode(8);

        $sql = "SELECT reviewCode FROM review";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $row) {
            if ($reviewCode === $row['reviewCode']) {
                $reviewCode = $this->generateVerificationCode(9);
                break;
            }
        }


        $sql = "Select usedLanguage from user where userId=:userId";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':userId', $_POST['ownerId']);
        $stmt->execute();
        $usedLanguage = $stmt->FetchColumn();


        $sql = "INSERT INTO review( reviewCode, userId, veterinarianId)
VALUES (:reviewCode,:userId,:veterinarianId)";
        $stmt = $this->connection->prepare($sql);

        $stmt->bindParam(':reviewCode', $reviewCode);
        $stmt->bindParam(':userId', $_POST['ownerId']);
        $stmt->bindParam(':veterinarianId', $_SESSION['userId']);
        $stmt->execute();
        $_SESSION['usedLanguage'] = $usedLanguage;
        $_SESSION['ownerMail'] = $_POST['ownerMail'];
        $_SESSION['reviewLink'] = 'http://localhost/Humanz_Pets/reviewVeterinarian.php?reviewCode=' . $reviewCode;
        header('Location:mail.php');
        exit();
    }

    public function mailAddAndPasswordChange()
    {
        if (!isset($_POST['mailReset'])) {
            $_SESSION['message'] = "This Email address doesn't exist!";
            header('Location: logIn.php');
            exit();
        }

        $mail = $_POST['mailReset'];
        $result = $this->getEmailDetails($mail, 'user');
        $resultVet = $this->getEmailDetails($mail, 'veterinarian');

        if ($result || $resultVet) {
            $verification_code = $this->generateVerificationCode();
            $verification_time = date("Y-m-d H:i:s", time() + 600); // 10 minutes

            $table = $result ? 'user' : 'veterinarian';
            $this->updateVerificationDetails($mail, $verification_code, $verification_time, $table);

            $_SESSION['mailReset'] = $mail;
            $_SESSION['resetCode'] = $verification_code;
            header('Location: mail.php');
            exit();
        }

        $this->logError($mail, "Password change", "Not registered E-mail!", "The email or the password doesn't match up!");
        header('Location: logIn.php');
        exit();
    }

    private function getEmailDetails($mail, $table)
    {
        $sql = "SELECT * FROM $table WHERE {$table}Mail = :mail";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':mail', $mail);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function generateVerificationCode($length = 6)
    {
        return substr(number_format(time() * rand(), 0, '', ''), 0, $length);
    }

    private function updateVerificationDetails($mail, $code, $time, $table)
    {
        $sql = "UPDATE $table SET passwordValidation = :code, passwordValidationTime = :time WHERE {$table}Mail = :mail";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':mail', $mail);
        $stmt->execute();
    }

    private function logError($mail, $logType, $logText, $logMessage)
    {
        $this->errorLogInsert($mail, $logText, $logType, $logMessage);
    }

    public function resetPassword()
    {
        if (isset($_POST['mail'], $_POST['resetPassword'], $_POST['confirmPassword'])) {
            $mail = $_POST['mail'];
            $pass = $_POST['resetPassword'];
            $pass2 = $_POST['confirmPassword'];

            // Validate passwords
            if ($pass === '') {
                $_SESSION['message'] = "The <b>Password</b> is not filled out.";
                header('Location: ' . $_SESSION['backPic']);
                exit();
            }
            if ($pass2 === '') {
                $_SESSION['message'] = "The <b>Confirmation Password</b> is not filled out.";
                header('Location: ' . $_SESSION['backPic']);
                exit();
            }
            if ($pass !== $pass2) {
                $_SESSION['message'] = "The Passwords do not match.";
                header('Location: ' . $_SESSION['backPic']);
                exit();
            }
            if (!preg_match("/[a-z]/", $pass)) {
                $_SESSION['message'] = "The <b>Password</b> does not contain <b>Lower case</b> letters.";
                header('Location: ' . $_SESSION['backPic']);
                exit();
            }
            if (!preg_match("/[A-Z]/", $pass)) {
                $_SESSION['message'] = "The <b>Password</b> does not contain <b>Upper case</b> letters.";
                header('Location: ' . $_SESSION['backPic']);
                exit();
            }
            if (!preg_match("/[0-9]/", $pass)) {
                $_SESSION['message'] = "The <b>Password</b> does not contain <b>Numbers</b>.";
                header('Location: ' . $_SESSION['backPic']);
                exit();
            }
            if (strlen($pass) < 8) {
                $_SESSION['message'] = "The <b>Password</b> must be at least <b>8 characters long</b>.";
                header('Location: ' . $_SESSION['backPic']);
                exit();
            }

            // Hash the new password
            $newPassword = password_hash($pass, PASSWORD_BCRYPT);

            // Check which table the email exists in
            try {
                $sql = "SELECT 'user' AS userType FROM user WHERE userMail = :email1
                    UNION
                    SELECT 'vet' AS userType FROM veterinarian WHERE veterinarianMail = :email2";
                $stmt = $this->connection->prepare($sql);
                $stmt->bindParam(':email1', $mail, PDO::PARAM_STR);
                $stmt->bindParam(':email2', $mail, PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    $userType = $result['userType'];
                    $table = ($userType === 'user') ? 'user' : 'veterinarian';
                    $column = ($userType === 'user') ? 'userPassword' : 'veterinarianPassword';
                    $emailColumn = ($userType === 'user') ? 'userMail' : 'veterinarianMail';

                    // Update password
                    $updateSql = "UPDATE $table SET verify=1,$column = :newPassword WHERE $emailColumn = :email";
                    $updateStmt = $this->connection->prepare($updateSql);
                    $updateStmt->bindParam(':newPassword', $newPassword, PDO::PARAM_STR);
                    $updateStmt->bindParam(':email', $mail, PDO::PARAM_STR);
                    if ($updateStmt->execute()) {
                        $_SESSION['message'] = "Password updated successfully. You can now log in.";
                        header('Location: logIn.php');
                        exit();
                    } else {
                        $_SESSION['message'] = "Failed to update the password. Please try again.";
                        header('Location: ' . $_SESSION['backPic']);
                        exit();
                    }
                } else {
                    $_SESSION['message'] = "Email not found in our records." . $mail;
                    header('Location: ' . $_SESSION['backPic']);
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION['message'] = "An error occurred: " . $e->getMessage();
                header('Location: ' . $_SESSION['backPic']);
                exit();
            }
        } else {
            $_SESSION['message'] = "Required data is missing.";
            header('Location: ' . $_SESSION['backPic']);
            exit();
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

    public function updatePet()
    {
        if (empty($_FILES['picture']))
            $petPicture = $_SESSION['petPicture'];
        else {
            $petPicture = $this->picture($_SESSION['backPic']);
            if ($petPicture == 4) {
                $petPicture = $_SESSION['petPicture'];
                unset($_SESSION['message']);
            }
        }
        $petName = ucfirst(strtolower($_POST['petName']));
        $bred = ucfirst(strtolower($_POST['bred']));
        $specie = ucfirst(strtolower($_POST['specie']));
        $sql = 'Update pet set petName=:petName,bred=:bred,petSpecies=:petSpecies,petPicture=:petPicture where petId="' . $_SESSION['petId'] . '"';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':petName', $petName);
        $stmt->bindParam(':bred', $bred);
        $stmt->bindParam(':petSpecies', $specie);
        $stmt->bindParam(':petPicture', $petPicture);
        $stmt->execute();

        if (isset($_POST['petUpdate']))
            header('Location:pet.php?email=' . $_SESSION['email']);
        else
            header('Location:registerAnimal.php');
        exit();
    }

    public function insertReservation()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $veterinarianId = $_POST['veterinarianId'];
            $selectedPetId = $_POST['petId'] ?? null;
            $reservationDate = $_POST['day'] ?? null;
            $reservationStart = $_POST['reservationTimeStart'] ?? null;
            $reservationEnd = $_POST['reservationTimeEnd'] ?? null;

            if ($selectedPetId && $reservationDate && $reservationStart && $reservationEnd) {
                // Check if the pet already has 5 reservations for the day
                $today = date("Y-m-d");
                $reservationCheckQuery = $this->connection->prepare(
                    "SELECT COUNT(*) AS reservationCount FROM reservation 
             WHERE petId = :petId AND reservationDay >= :today"
                );
                $reservationCheckQuery->execute([
                    ':petId' => $selectedPetId,
                    ':today' => $today
                ]);
                $reservationCount = $reservationCheckQuery->fetch(PDO::FETCH_ASSOC)['reservationCount'] ?? 0;

                if ($reservationCount < 1) {
                    // Insert the reservation
                    $insertQuery = $this->connection->prepare(
                        "INSERT INTO reservation (petId, veterinarianId, reservationDay, reservationTime, period) 
                 VALUES (:petId, :veterinarianId, :reservationDay, :reservationStart, :reservationEnd)"
                    );
                    $insertQuery->execute([
                        ':petId' => $selectedPetId,
                        ':veterinarianId' => $veterinarianId,
                        ':reservationDay' => $reservationDate,
                        ':reservationStart' => $reservationStart,
                        ':reservationEnd' => $reservationEnd
                    ]);

                    $_SESSION['message'] = "Reservation successfully created!";
                } else {
                    $_SESSION['message'] = "You already have too many reservations for this pet.";
                }
            } else {
                $_SESSION['message'] = "All fields are required.";
            }
        }
        header('Location:book_apointment.php?email=' . $_SESSION['email'] . "&veterinarian=" . $_POST['veterinarian']);

    }

    public function deleteReservation()
    {
        $sql = 'DELETE FROM reservation
WHERE reservationId = :reservationId
  AND (
      reservationDay > CURDATE() 
      OR (reservationDay = CURDATE() AND reservationTime > ADDTIME(NOW(), "01:00:00"))
  );

';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':reservationId', $_POST['reservationId']);
        $stmt->execute();
        $result = $stmt->rowCount();
        if ($result)
            $_SESSION['message'] = "Reservation successfully deleted.";
        else
            $_SESSION['message'] = "Failed to delete the reservation. Please try again.";
        header('Location:book_apointment.php?email=' . $_SESSION['email'] . "&veterinarian=" . $_POST['veterinarian']);

        exit();
    }

    public function deletePet()
    {
        $sql = 'delete from pet where petId=:petId';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':petId', $_SESSION['petId']);
        $stmt->execute();
        if (isset($_POST['petUpdate']))
            header('Location:pet.php?email=' . $_SESSION['email']);
        else
            header('Location:registerAnimal.php');
        exit();
    }

    public function buyProduct()
    {
        if (!empty($_POST['productId']) && !empty($_POST['quantity'])) {
            $productId = $_POST['productId'] ?? null;
            $productName = $_POST['productName'] ?? null;
            $productPrice = $_POST['productPrice'] ?? null;
            $productPicture = $_POST['productPicture'] ?? null;
            $userId = $_SESSION['userId'] ?? null;
            $sum = $_POST['quantity'] ?? null;

            $sql = "INSERT INTO user_product_relation( userId, productName,productPicture,productId,sum, price) 
VALUES (:userId,:productName,:productPicture,:productId,:sum, :price)";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':productName', $productName);
            $stmt->bindParam(':productPicture', $productPicture);
            $stmt->bindParam(':productId', $productId);
            $stmt->bindParam(':sum', $sum);
            $stmt->bindParam(':price', $productPrice);
            $stmt->execute();


        } else {
            $_SESSION['message'] = "Something is missing in the product parameters";
        }
        header('Location:products.php');
        exit();
    }

    public function addVet()
    {
        if (isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['tel']) && isset($_POST['mail'])) {

            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $tel = $_POST['tel'];
            $mail = $_POST['mail'];
            $language = $_POST['lang'];

            $_SESSION["workerEmail"] = $mail;

            try {
                $time2 = time();
                $time3 = date("Y-m-d H:i:s", $time2);
                $time = time() + 60 * 10;
                $verification_time = date("Y-m-d H:i:s", $time);

                // Check if user already exists
                $sql = "SELECT veterinarianMail, verify, verification_time FROM veterinarian WHERE veterinarianMail = :mail";
                $stmt = $this->connection->prepare($sql);
                $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $_SESSION['message'] = "mail already exists";
                    header('Location: addVet.php');
                    exit();
                }
                $sql = "SELECT userMail, verify, verification_time FROM user WHERE userMail = :mail";
                $stmt = $this->connection->prepare($sql);
                $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $_SESSION['message'] = "mail already exists";
                    header('Location: addVet.php');
                    exit();
                }
                $kep = "logInPic.png";

                $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 7);
                $pass = null;
                $pass_vali = null;
                $verification = 0;
                $banned_time = null;
                $picture = "logInPic.png";
                $verification_code_expire = null;

                // Insert user data into the database
                $sql = "INSERT INTO veterinarian 
                    (firstName, lastName, phoneNumber, veterinarianMail, veterinarianPassword, registrationTime,profilePic, verification_code, verify, verification_time, passwordValidation, passwordValidationTime, usedLanguage) 
                    VALUES (:fname, :lname, :tel, :mail, :pass, :reg_time,:profilePic, :verification_code, :verify, :verification_time, :pass_vali, :banned_time, :usedLanguage)";
                $stmt = $this->connection->prepare($sql);

                $stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
                $stmt->bindParam(':lname', $lname, PDO::PARAM_STR);
                $stmt->bindParam(':tel', $tel, PDO::PARAM_STR);
                $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                $stmt->bindParam(':reg_time', $time3, PDO::PARAM_STR);
                $stmt->bindParam(':profilePic', $picture, PDO::PARAM_STR);
                $stmt->bindParam(':verification_code', $verification_code, PDO::PARAM_STR);
                $stmt->bindParam(':verify', $verification, PDO::PARAM_INT);
                $stmt->bindParam(':verification_time', $verification_time, PDO::PARAM_STR);
                $stmt->bindParam(':pass_vali', $pass_vali, PDO::PARAM_NULL);
                $stmt->bindParam(':banned_time', $banned_time, PDO::PARAM_NULL);
                $stmt->bindParam(':usedLanguage', $language, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $_SESSION['workerLink'] = "http://localhost/Humanz_Pets/resetPassword.php?mail=" . $mail . "&token=" . $verification_code;
                    $_SESSION['message'] = "Worker added Successfully!";
                    $_SESSION['text'] = "<h2>Registration</h2>";
                    $_SESSION['verification_code'] = $verification_code;
                    $_SESSION['veterinarianEmail'] = $mail;
                    header('Location: mail.php');
                    exit();
                } else {
                    $_SESSION['message'] = "Error occurred during registration.";
                    header('Location: registration.php?token=' . $_SESSION['token']);
                    exit();
                }

            } catch (PDOException $e) {
                $_SESSION['message'] = "An error occurred: " . $e->getMessage();
            }
        }
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
                if ($specie == "Specie") {
                    $_SESSION['message'] = "Select a Specie.";
                    header('location:registerAnimal.php');
                    exit();
                }
                if ($_SESSION['petPicture'] == 4) {
                    $_SESSION['message'] = "Select a image for your pet.";
                    header('location:registerAnimal.php');
                    exit();
                }
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
                    $_SESSION['message'] = "You registered your animal successfully. Now <b>choose the veterinarian</b><br>
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

    public function updateProduct()
    {

        try {
            $productId = ucfirst(strtolower(trim($_POST["productId"])));
            $productName = ucfirst(strtolower(trim($_POST["productName"])));
            $price = ucfirst(strtolower(trim($_POST["price"])));

            $picture = $this->picture($_SESSION['backPic']);
            if ($picture == 4) {
                $picture = $_SESSION['updateProductPicture'];
            }
            $description = ucfirst(strtolower(trim($_POST["productDescription"])));

            // Insert the pet data into the database
            $stmt = "UPDATE product 
SET productName = :productName, 
    productCost = :price, 
    productPicture = :productPicture, 
    description = :productDescription, 
    productRelease = NOW() 
WHERE productId = :productId;
";
            $query = $this->connection->prepare($stmt);
            $query->bindParam(':productId', $productId, PDO::PARAM_STR);
            $query->bindParam(':productName', $productName, PDO::PARAM_STR);
            $query->bindParam(':price', $price, PDO::PARAM_STR);
            $query->bindParam(':productPicture', $picture, PDO::PARAM_STR);
            $query->bindParam(':productDescription', $description, PDO::PARAM_STR);


            if ($query->execute()) {
                header("Location: products.php");
                exit();
            } else {
                throw new Exception("Failed to register the pet.");
            }
        } catch (Exception $e) {
            $_SESSION['message'] = "Error: " . $e->getMessage();
            header("Location: addProduct.php");
            exit();
        }
    }

    public function addProduct()
    {
        if (isset($_POST['productName']) && isset($_POST['price']) && isset($_POST['productDescription']) && isset($_SESSION['backPic'])) {
            try {
                $productName = ucfirst(strtolower(trim($_POST["productName"])));
                $price = ucfirst(strtolower(trim($_POST["price"])));
                $picture = $this->picture($_SESSION['backPic']);
                $description = ucfirst(strtolower(trim($_POST["productDescription"])));

                // Insert the pet data into the database
                $stmt = "INSERT INTO product (productName, productCost, productPicture, description, productRelease)
                    VALUES (:productName, :price,:productPicture,:productDescription, NOW())";
                $query = $this->connection->prepare($stmt);
                $query->bindParam(':productName', $productName, PDO::PARAM_STR);
                $query->bindParam(':price', $price, PDO::PARAM_STR);
                $query->bindParam(':productPicture', $picture, PDO::PARAM_STR);
                $query->bindParam(':productDescription', $description, PDO::PARAM_STR);


                if ($query->execute()) {
                    header("Location: products.php");
                    exit();
                } else {
                    throw new Exception("Failed to register the pet.");
                }
            } catch (Exception $e) {
                $_SESSION['message'] = "Error: " . $e->getMessage();
                header("Location: addProduct.php");
                exit();
            }
        }
        else{
            $_SESSION['message'] = "Please fill in all the fields.";
            header("Location: addProduct.php");
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

    public function userCheck1($fname, $lname, $email, $tel, $location)
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
        if ($tel != "") {
            if (strlen($tel) != 10 && strlen($tel) != 11) {
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
        if (isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['tel']) && isset($_POST['mail']) && isset($_POST['pass']) && isset($_POST['pass2'])) {


            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $tel = $_POST['tel'];

            $usedLanguage = $_POST['lang'];
            $mail = $_POST['mail'];
            $_SESSION["email"] = $mail;
            $pass = $_POST['pass'];
            $pass2 = $_POST['pass2'];

            $this->userCheck1($fname, $lname, $mail, $tel, "registration.php");

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
                  privilage, registrationTime, verification_time, banned, banned_time, passwordValidation, passwordValidationTime, usedLanguage) 
        VALUES (:firstName, :lastName, :phoneNumber, :userMail, :userPassword, 
                :verification_code, :verify, :profilePic, :privilage, :registrationTime, 
                :verification_time, :banned, :banned_time, :passwordValidation, :passwordValidationTime, :usedLanguage)";

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
                $stmt->bindParam(':usedLanguage', $usedLanguage, PDO::PARAM_STR);

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
                exit();
            }
        } else {
            $_SESSION['message'] = "Error occurred during registration!";
            exit();
        }

    }

    public function userModifyData($fname, $lname, $tel, $location)
    {
        if (empty($fname) || empty($lname) || empty($tel)) {
            $_SESSION['message'] = "The fields <b>has to be filled.</b>";
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
        if ($tel != "") {
            if (strlen($tel) != 10 && strlen($tel) != 11) {
                $_SESSION['message'] = "The <b>Phone Number</b> does not exist!";
                header('Location: ' . $location);
                exit();
            }
        }


    }

    public function modifyUser()
    {
        $count = 0;
        $phoneNumber = $_POST['tel'];

        // Check user privilege
        if ($_SESSION['privilage'] == 'Veterinarian') {
            $table = 'veterinarian';
            $sql = $this->connection->prepare("SELECT firstName, lastName, phoneNumber,usedLanguage FROM veterinarian WHERE veterinarianMail = ?");
        } else {
            $table = 'user';
            $sql = $this->connection->prepare("SELECT firstName, lastName, phoneNumber,usedLanguage privilage FROM user WHERE userMail = ?");
        }
// Prepare the initial query to retrieve existing user details

        $sql->execute([$_SESSION['email']]);
        $result = $sql->fetch(PDO::FETCH_ASSOC);


// Calling userModifyData function with posted data
        $this->userModifyData($_POST['firstName'], $_POST['lastName'], $_POST['tel'], "modify.php");


// Check if user data was found and update if necessary
        if ($result) {

            $empty = 0;
            // Update first name if provided
            if (!empty($_POST['firstName'])) {
                $firstName = ucfirst(strtolower($_POST['firstName']));
                $sql = $this->connection->prepare("UPDATE $table SET firstName = ? WHERE " . $table . "Mail = ?");
                $sql->execute([$firstName, $_SESSION['email']]);

                $_SESSION['firstName'] = $firstName;
                $_SESSION['name'] = $_SESSION['firstName'] . " " . $_SESSION['lastName'];
                $_SESSION['message'] = "First name is modified";
                $count++;
            }


            // Update last name if provided
            if (!empty($_POST['lastName'])) {
                $lastName = ucfirst(strtolower($_POST['lastName']));
                $sql = $this->connection->prepare("UPDATE $table SET lastName = ? WHERE " . $table . "Mail = ?");
                $sql->execute([$lastName, $_SESSION['email']]);

                $_SESSION['lastName'] = $lastName;
                $_SESSION['name'] = $_SESSION['firstName'] . " " . $_SESSION['lastName'];
                $_SESSION['message'] = "Last name is modified";
                $count++;
            }

            // Update phone number if provided
            if (!empty($_POST['tel'])) {
                $sql = $this->connection->prepare("UPDATE $table SET phoneNumber = ? WHERE " . $table . "Mail = ?");
                $sql->execute([$phoneNumber, $_SESSION['email']]);
                $_SESSION['message'] = "Phone number is modified";
                $_SESSION['phone'] = $phoneNumber;
                $count++;
            }
            if (!empty($_POST['usedLanguage'])) {
                $usedLanguage=$_POST['usedLanguage'];
                $sql = $this->connection->prepare("UPDATE $table SET usedLanguage = ? WHERE " . $table . "Mail = ?");
                $sql->execute([ $usedLanguage, $_SESSION['email']]);
                $_SESSION['message'] = "Language is modified";
                $_SESSION['userLang'] =  $usedLanguage;
                $count++;
            }

        }

// Set session message based on whether any changes were made
        $_SESSION['message'] = $count > 0 ? "Changes saved" : "Empty data cannot be saved!";

        $stmt = "SELECT firstName, lastName, phoneNumber FROM $table WHERE " . $table . "Id = :Id";
        $stmt = $this->connection->prepare($stmt);
        $stmt->bindParam(':Id', $_SESSION['userId']);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $_SESSION['qrCodeFile'] = $this->createQrCode($row['firstName'] . ' ' . $row['lastName'], $row['phoneNumber']);
        }
        if ($_SESSION['privilage'] != 'veterinarian') {
            $stmt = "UPDATE qr_code qr
INNER JOIN $table u ON qr.userId = u.userId
SET qr.qrCodeName = :qrCodeName
WHERE u.userId = :userId";
            $stmt = $this->connection->prepare($stmt);
            $stmt->bindParam(':userId', $_SESSION['userId']);
            $stmt->bindParam(':qrCodeName', $_SESSION['qrCodeFile']);
            $stmt->execute();
        }
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
                        $_SESSION['message'] = "File is too big! Is has to be smaller than 300KB!";
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

                        if ($_SESSION['privilage'] != 'Veterinarian')
                            $query = $this->connection->prepare("UPDATE user SET profilePic = :profilePic WHERE userMail = :userMail");
                        else {
                            $query = $this->connection->prepare("UPDATE veterinarian SET profilePic = :profilePic WHERE veterinarianMail = :userMail");

                        }
                        $query->bindValue(":profilePic", $new_file_name, PDO::PARAM_STR);
                        $query->bindValue(":userMail", $_SESSION['email'], PDO::PARAM_STR);
                        $query->execute();
                        $_SESSION['profilePic'] = $new_file_name;
                        // Redirect to login page after successful upload
                        header('Location: ' . $_SESSION['backPic']);
                        exit(); // Exit after redirection
                    } elseif ($target == "addVet.php") {

                        $query = $this->connection->prepare("UPDATE veterinarian SET profilePic = :profilePic
                    WHERE veterinarianMail = :veterinarianMail");
                        $query->bindValue(":profilePic", $new_file_name, PDO::PARAM_STR);
                        $query->bindValue(":veterinarianMail", $_POST['email'], PDO::PARAM_STR);
                        $query->execute();

                        return $new_file_name;
                    } elseif ($target == 'registerAnimal.php' || $target == "addProduct.php") {
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

    public function language()
    {

        // Determine the language: prioritize GET parameter, then userLang, then 'en'
        if (isset($_GET['lang'])) {
            $lang = $_GET['lang']; // User selected a language
            $_SESSION['lang'] = $lang; // Save selected language in session
        } else {
            $lang = $_SESSION['userLang'] ?? 'en'; // Default to userLang or 'en'
            $_SESSION['lang'] = $lang; // Set session language to default
        }
        return $lang;
        // Include the language file
//            include "lang_$lang.php";

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

    public function fetchUserByEmail($email)
    {
        try {
            // Check the `user` table first
            $sql = "SELECT 'user' AS userType, userMail AS mail, userPassword AS password, banned AS banned,
                       firstName, lastName, profilePic, userId, phoneNumber, privilage, usedLanguage
                FROM user
                WHERE userMail = :email";

            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // If a result is found, return it
            if ($result) {
                return $result;
            }

            // Otherwise, check the `veterinarian` table
            $sql = "SELECT 'vet' AS userType, veterinarianMail AS mail, veterinarianPassword AS password, banned AS banned,
         firstName, lastName, profilePic, veterinarianId, phoneNumber, usedLanguage
                FROM veterinarian
                WHERE veterinarianMail = :email";

            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $vetResult = $stmt->fetch(PDO::FETCH_ASSOC);

            // Debugging statement
            if (!$vetResult) {
                error_log("No data found for veterinarian email: $email");
            }

            return $vetResult;
        } catch (PDOException $e) {
            // Log the error and return null
            error_log("Database error: " . $e->getMessage());
            $this->errorLogInsert($email, "Database error: " . $e->getMessage(), "Fetch User/Vet", "Error during query");
            return null;
        }
    }


    public function login()
    {
        if (isset($_POST['mail'], $_POST['pass'])) {
            $mail = $_POST['mail'];
            $password = $_POST['pass'];

            // Fetch user or veterinarian details
            $result = $this->fetchUserByEmail($mail);

            if ($result) {
                error_log("Fetched result: " . print_r($result, true)); // Debugging
                sleep(2); // Delay for security reasons
                if (empty($result['password'])) {
                    $_SESSION['message'] = "The account did not set up a password!";
                } else {
                    if (password_verify($password, $result['password'])) {
                        if ($result['banned']) {
                            $_SESSION['message'] = "You have been banned from our website!";
                        } else {
                            $_SESSION['email'] = $result['mail'];
                            $_SESSION['firstName'] = $result['firstName'] ?? ''; // Default if missing
                            $_SESSION['lastName'] = $result['lastName'] ?? ''; // Default if missing
                            $_SESSION['name'] = $_SESSION['firstName'] . " " . $_SESSION['lastName'];
                            $_SESSION['profilePic'] = $result['profilePic'] ?? ''; // Default if missing
                            $_SESSION['phone'] = $result['phoneNumber'] ?? ''; // Default if missing
                            $_SESSION['userLang'] = $result['usedLanguage'] ?? '';
                            // Veterinarian-specific session and cookies
                            if ($result['userType'] === 'vet') {

                                $_SESSION['userId'] = $result['veterinarianId'] ?? ''; // Default if missing
                                $_SESSION['privilage'] = 'Veterinarian';
                            } else {
                                // User-specific session and cookies
                                $_SESSION['userId'] = $result['userId'] ?? ''; // Default if missing
                                $_SESSION['privilage'] = $result['privilage'] ?? ''; // Default if missing
                            }

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
                $_SESSION['message'] = "Something went wrong, maybe the email is not registered!";
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
                $result = $this->fetchUserByEmail($_SESSION['email']);
                if($result['banned']){
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
                if (isset($_SESSION['backPic']))
                    $backPage = $_SESSION['backPic'];
                $mail = $_SESSION['email'];
                if ($_SESSION['privilage'] == 'user') {
                    $sql = "select petId from pet where userId=:userId and veterinarId is NULL";
                    $stmt = $this->connection->prepare($sql);
                    $stmt->bindParam(":userId", $_SESSION["userId"]);
                    $stmt->execute();
                    if ($stmt->rowCount() == 1 && $backPage != "updateAnimal.php") {
                        $_SESSION['message'] = 'You have to choose a veterinarian for your animal,<br> before you can go further<br><br><a href="functions.php?action=logOut">Log out</a> ';
                        if ($currentPage != 'selectVeterinarian.php') {
                            header("Location:selectVeterinarian.php");
                            exit();
                        }
                    }
                }

                unset($_COOKIE['last_activity']);
                unset($_COOKIE['email']);
                unset($_COOKIE['name']);
                unset($_COOKIE['profilePic']);
                unset($_COOKIE['userId']);
                unset($_COOKIE['phone']);
                unset($_COOKIE['privilage']);

                setcookie("email", $_SESSION['email'], time() + 10 * 60, "/");
                setcookie("name", $_SESSION['name'], time() + 10 * 60, "/");
                setcookie("profilePic", $_SESSION['profilePic'], time() + 10 * 60, "/");
                setcookie("userId", $_SESSION['userId'], time() + 10 * 60, "/");
                setcookie("phone", $_SESSION['phone'], time() + 10 * 60, "/");
                setcookie("privilage", $_SESSION['privilage'], time() + 10 * 60, "/");
                setcookie("last_activity", time(), time() + 10 * 60, "/");
                if ($_SESSION['privilage'] == 'user') {
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
                }

            } elseif (isset($_COOKIE['email'])) {
                $result = $this->fetchUserByEmail($_COOKIE['email']);
                if($result['banned']){
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

                setcookie("email", $_SESSION['email'], time() + 10 * 60, "/");
                setcookie("name", $_SESSION['name'], time() + 10 * 60, "/");
                setcookie("profilePic", $_SESSION['profilePic'], time() + 10 * 60, "/");
                setcookie("userId", $_SESSION['userId'], time() + 10 * 60, "/");
                setcookie("phone", $_SESSION['phone'], time() + 10 * 60, "/");
                setcookie("privilage", $_SESSION['privilage'], time() + 10 * 60, "/");
                setcookie("last_activity", time(), time() + 10 * 60, "/");
                if ($_SESSION['privilage'] == 'user') {

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
