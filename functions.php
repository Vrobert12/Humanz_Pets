<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include "config.php";
require 'phpqrcode/qrlib.php';
require "vendor/autoload.php";
if (isset($_SESSION['lang']))
    include "lang_" . $_SESSION['lang'] . ".php";
else
    include "lang_en.php";
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
                case 'deletePicture':
                    $this->deletePicture();
                    break;
                case 'insertDescription':
                    $this->insertDescription();
                    break;
                case 'PayFromCart':
                    $this->payProduct();
                    break;
                case 'PayAllFromCart':
                    $this->payAllProduct();
                    break;
                default:
                    $_SESSION['message'] = ERROR;
                    header('Location:index.php');
                    exit();
            }
        } elseif (isset($_GET['action'])) {
            if ($_GET['action'] === 'logOut') {
                $this->logOut();
            }
        }
    }
    public function payAllProduct()
    {
        $stmt="UPDATE user_product_relation SET productPayed=1 WHERE userId=:userId AND productPayed=0";
        $stmt = $this->connection->prepare($stmt);
        $stmt->bindParam(':userId', $_POST['userId']);
        $stmt->execute();
        header('Location:usersProducts.php?user='.$_POST['userId']);
        exit();
    }
public function payProduct()
{
    $stmt="UPDATE user_product_relation SET productPayed=1,payedDay=NOW() WHERE userProductRelationId=:userProductRelationId";
    $stmt = $this->connection->prepare($stmt);
    $stmt->bindParam(':userProductRelationId', $_POST['cartId']);
    $stmt->execute();
    header('Location:usersProducts.php?user='.$_POST['userId']);
    exit();
}
    public function blurSwearWords($text) {
        $swearWords = [
            // **Hungarian**
            'bazdmeg', 'kurva', 'geci', 'fasz', 'buzi', 'picsa', 'szar', 'seggfej', 'faszfej', 'balfasz',
            'csicska', 'szopás', 'szopni', 'buzeráns', 'rohadt', 'szemét', 'mocskos', 'dögölj', 'dög',
            'kibaszott', 'kibaszás', 'kibaszni', 'megduglak', 'baszódj meg', 'rohadj meg', 'nyomorult',
            'kretén', 'idióta', 'hülye', 'dögvész', 'fostenger', 'fosch', 'szardarab', 'pöcs',
            'lúzer', 'nyomorék', 'segg', 'seggdugasz', 'köcsög', 'tahó', 'barmok', 'ostoba', 'szarházi',
            'patkány', 'paraszt', 'seggfej', 'anyád', 'anyád picsája', 'kurvapecér', 'féreg', 'ostoba állat',

            // **Serbian (Latin)**
            'jebem', 'pička', 'kurac', 'govno', 'majku ti', 'sisaš', 'picka materina', 'smrad', 'peder',
            'jebote', 'kurvina', 'kurvetina', 'pičketina', 'govnara', 'jebiga', 'crkni', 'jebote bog',
            'jebem ti mater', 'puši kurac', 'mamu ti jebem', 'smradu', 'guzica', 'jebanje', 'jebati',
            'krmak', 'čmar', 'đubre', 'stoko', 'seljačina', 'bolesnik', 'retard', 'glupson', 'idiot',
            'budalo', 'majmune', 'konju', 'svinjo', 'kretenčino', 'kurvinski sin', 'jebote pas',
            'pička materina', 'bog te jebo', 'kurvin sin', 'balvan', 'govnjar', 'smrdljivi idiot',
            'konjska kurčina', 'jebem ti sve po spisku', 'crkni smrade', 'mamu ti razvalim',
            'usmrđena pičko', 'jebem ti sunce', 'siso', 'degeneriku', 'glupane', 'majmunče',
            'smeće', 'kretenu', 'kurvica', 'proklet bio', 'jebeni konj', 'pičko', 'šupak', 'mrš u pičku materinu',

            // **English**
            'fuck', 'shit', 'bitch', 'asshole', 'bastard', 'cunt', 'dick', 'motherfucker', 'prick',
            'whore', 'slut', 'fucker', 'son of a bitch', 'cock', 'piss', 'damn', 'douchebag',
            'dumbass', 'jackass', 'retard', 'bullshit', 'dipshit', 'twat', 'wanker', 'dickhead',
            'arsehole', 'scumbag', 'bloody hell', 'goddamn', 'hell', 'dumbfuck', 'shithead',
            'fuckface', 'nutjob', 'pisshead', 'jerkoff', 'cocksucker', 'fucked up', 'motherfucking',
            'cockface', 'dumbshit', 'asshat', 'buttmunch', 'crapface', 'shitfaced', 'twatface',
            'fucktard', 'wankstain', 'shiteater', 'cumdumpster', 'cockgobbler', 'bitchass',
            'dickbag', 'assclown', 'cumbucket', 'cumstain', 'fuckwad', 'dickweasel', 'horseshit',
            'pissflaps', 'cuntface', 'dickhole', 'fuckwhit', 'shitlord', 'shitbag', 'cockmuncher',
            'cockwomble', 'twatwaffle', 'arsewipe', 'knobhead', 'wankpuffin', 'asswipe',
            'cocknugget', 'clusterfuck', 'shitgibbon', 'fucknut', 'fuckstick', 'bitchtits',
            'assgoblin', 'knobjockey', 'dickcheese', 'fuckbucket', 'wankshaft', 'bellend',
            'twatknuckle', 'shitstorm', 'shitehawk', 'fuckpuddle', 'shitbrick', 'fuckmonkey',
            'twunt', 'fuckrag', 'shitstain', 'titsucker', 'cuntmuffin', 'pissguzzler',
            'cockwrench', 'shitwhistle', 'dickfuck', 'shitblimp', 'fuckmuppet', 'jizzstain',
            'spunktrumpet', 'cockrocket', 'fuckcannon'
        ];

        $textLower = strtolower($text); // Convert text to lowercase for case-insensitive checking

        foreach ($swearWords as $word) {
            // Case-insensitive word boundary match, looking for individual characters
            $pattern = '/(?<=^|\s|\b|\W)' . preg_quote($word, '/') . '(?=\s|\b|\W|$)/i';

            // Replace with stars, preserving the first and last characters
            $replacement = function ($matches) use ($word) {
                $match = $matches[0];
                return $word[0] . str_repeat('*', strlen($match) - 2) . $word[strlen($match) - 1];
            };

            // Apply replacement
            $text = preg_replace_callback($pattern, $replacement, $text);
        }

        return $text;
    }



    public function insertDescription()
{
    if(isset($_POST['vetDescription'])) {
        $vetDescription = $this->blurSwearWords($_POST['vetDescription']);
        $sql = 'UPDATE veterinarian set veterinarianDescription=:description WHERE veterinarianId = :veterinarianID';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':description', $vetDescription);
        $stmt->bindParam(':veterinarianID', $_SESSION['userId']);
        $stmt->execute();
        $_SESSION['message'] = DESCRIPTION_VET_UPDATED;
        header('Location:addDescription.php');
        exit();
    }
}
    public function deletePicture()
    {
        if ($_POST['table'] == 'veterinarian') {

            $sql = "UPDATE veterinarian SET profilePic=:profilePic WHERE veterinarianID=:veterinarianID";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':veterinarianID', $_POST['veterinarianId']);
        } else {
            $sql = "UPDATE user SET profilePic=:profilePic WHERE userID=:userID";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':userID', $_POST['userId']);
        }
        $stmt->bindValue(':profilePic', "logInPic.png");
        if ($stmt->execute()) {
            $_SESSION['message'] = PICSUCCESS;
            if($_SESSION['userId']==$_POST['userId'])
                $_SESSION['profilePic']='logInPic.png';
            header('Location:banSite.php');
            exit();
        }
    }

    public function deleteReservationByVet()
    {
        if (!empty($_POST['mailText'])) {
            // Prepare and execute the query to get user and pet details
            $sql = "SELECT u.userMail, u.usedLanguage, p.petName
                FROM user u
                INNER JOIN pet p ON u.userId = p.userId
                INNER JOIN reservation r ON p.petId = r.petId
                WHERE r.reservationId = :reservationId";

            $stmt2 = $this->connection->prepare($sql);
            $stmt2->bindValue(':reservationId', $_POST['reservationId']);
            $stmt2->execute();

            // Fetch the result and check if it's valid
            $resultLang = $stmt2->fetch();
            // Prepare and execute the deletion query
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



            if ($resultLang === false) {
                // Handle the case where no results are found
                $_SESSION['message'] = "No reservation found for the given ID.";
                header('Location: booked_users.php');
                exit();
            }

            if ($result && $resultLang) {  // Ensure both the deletion and the resultLang are valid
                // Set cancel subject based on language
                if ($resultLang['usedLanguage'] == "en") {
                    $_SESSION['cancelSubject'] = 'Reservation Cancelled';
                } elseif ($resultLang['usedLanguage'] == "hu") {
                    $_SESSION['cancelSubject'] = 'Vizsgálat lemondva';
                } else {
                    $_SESSION['cancelSubject'] = 'Pregled Otkazan';
                }

                // Store other session variables
                $_SESSION['mailText'] = $_POST['mailText'];
                $_SESSION['cancelEmail'] = $_POST['cancelEmail'];
                $_SESSION['message'] = RESDELSUC;

                // Redirect to mail.php
                header('Location: mail.php');
                exit();
            } else {
                // Handle failure case for reservation deletion
                $_SESSION['message'] = RESDELFAIL;
                header('Location: booked_users.php');
                exit();
            }
        } else {
            // Handle the case when mailText is empty
            $_SESSION['message'] = FILLMES;
            header('Location: booked_users.php');
            exit();
        }
    }


    public function vetBan()
    {
        if (isset($_POST['ban'])) {
            try {
                $time = time();
                $currentTime = date("Y-m-d H:i:s", $time);
                if ($_POST['ban'] == "yes") {
                    $_SESSION['message'] = UNBAN . $_POST['veterinarianId'];
                    $sql = "UPDATE veterinarian SET banned=0 WHERE veterinarianId=:veterinarianId";

                } else {
                    $_SESSION['message'] = BAN . $_POST['veterinarianId'];
                    $sql = "UPDATE veterinarian SET banned=1 WHERE veterinarianId=:veterinarianId";

                }
                $stmt = $this->connection->prepare($sql);
                $stmt->bindParam(':veterinarianId', $_POST['veterinarianId']);
                $stmt->execute();

                header('Location:' . $_SESSION['previousPage']);
                $_SESSION['previousPage'] = "";
                exit();
            } catch (Exception $e) {
                $_SESSION['message'] = ERROR;
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
                    $_SESSION['message'] = UNBAN;

                    $sql = "UPDATE user SET banned=0 WHERE userId=:userId";

                } else {
                    $_SESSION['message'] = UNBAN . ' ' . $_POST['userId'];

                    $sql = "UPDATE user SET banned=1 WHERE userId=:userId";

                }
                $stmt = $this->connection->prepare($sql);
                $stmt->bindParam(':userId', $_POST['userId']);
                $stmt->execute();

                header('Location:' . $_SESSION['previousPage']);
                $_SESSION['previousPage'] = "";
                exit();
            } catch (Exception $e) {
                $_SESSION['message'] = ERROR;
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
            $stmt->bindParam(":productId", $productId);
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
        $sql2 = "Select reservationDay FROM reservation WHERE reservationId = :reservationId";
        $stmt2 = $this->connection->prepare($sql2);
        $stmt2->bindParam(':reservationId', $_POST['reservationId']);
        $stmt2->execute();
        $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        $date = date("Y-m-d");
        if ($result2['reservationDay'] >= $date) {


            $_SESSION['message'] = FB;
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
        } else {
            $_SESSION['message'] = NOCHECK;
            header('Location:booked_users.php');
            exit();
        }
    }

    public function mailAddAndPasswordChange()
    {
        if (!isset($_POST['mailReset'])) {
            $_SESSION['message'] = NOEX;
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
            $_SESSION['mailResetLink'] = 'http://localhost/Humanz_Pets/resetPassword.php?verification_code='
                . $verification_code . '&verify_email=' . $mail;
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
                $_SESSION['message'] = PASSFILL;
                header('Location: ' . $_SESSION['backPic']);
                exit();
            }
            if ($pass2 === '') {
                $_SESSION['message'] = PASSFILL;
                header('Location: ' . $_SESSION['backPic']);
                exit();
            }
            if ($pass !== $pass2) {
                $_SESSION['message'] = PASSMATCH;
                header('Location: ' . $_SESSION['backPic']);
                exit();
            }
            if (!preg_match("/[a-z]/", $pass)) {
                $_SESSION['message'] = PASSNOLOW;
                header('Location: ' . $_SESSION['backPic']);
                exit();
            }
            if (!preg_match("/[A-Z]/", $pass)) {
                $_SESSION['message'] = PASSNOUP;
                header('Location: ' . $_SESSION['backPic']);
                exit();
            }
            if (!preg_match("/[0-9]/", $pass)) {
                $_SESSION['message'] = PASSNONUM;
                header('Location: ' . $_SESSION['backPic']);
                exit();
            }
            if (strlen($pass) < 8) {
                $_SESSION['message'] = PASSNOEIGHT;
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

                        $_SESSION['message'] = PASSUPD;
                        if (isset($_SESSION['email'])) {
                            header('Location: index.php');

                        } else {
                            $_SESSION['message'] = PASSUPD;
                            header('Location: logIn.php');
                        }
                        exit();
                    } else {
                        $_SESSION['message'] = PASSUPDF;
                        header('Location: ' . $_SESSION['backPic']);
                        exit();
                    }
                } else {
                    $_SESSION['message'] = EMAILNOTREG . $mail;
                    header('Location: ' . $_SESSION['backPic']);
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION['message'] = ERROR . $e->getMessage();
                header('Location: ' . $_SESSION['backPic']);
                exit();
            }
        } else {
            $_SESSION['message'] = DATAMIS;
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
        $saveDir = 'pictures/QRcodes/';
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
        $petName = ucfirst(strtolower($this->blurSwearWords($_POST['petName'])));
        $bred = ucfirst(strtolower($this->blurSwearWords($_POST['bred'])));
        $specie = ucfirst(strtolower($_POST['specie']));
        $sql = 'Update pet set petName=:petName,bred=:bred,petSpecies=:petSpecies,profilePic=:petPicture where petId="' . $_SESSION['petId'] . '"';
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

            $selectedPetId = $_POST['petId'] ?? null;
            $reservationDate = $_POST['day'] ?? null;
            $reservationStart = $_POST['reservationTimeStart'] ?? null;
            $reservationEnd = $_POST['reservationTimeEnd'] ?? null;
            $sql = "SELECT veterinarianId FROM veterinarian v inner join pet p ON v.veterinarianId=p.veterinarId WHERE petId=:petId";
            $sql = $this->connection->prepare($sql);
            $sql->bindValue(':petId', $selectedPetId);
            $sql->execute();
            $result = $sql->fetch();
            $veterinarianId = $result['veterinarianId'];

            if ($selectedPetId && $reservationDate && $reservationStart && $reservationEnd) {
                // Check if the pet already has 5 reservations for the day
                $today = date("Y-m-d");
                $reservationCheckQuery = $this->connection->prepare(
                    "SELECT COUNT(*) AS reservationCount FROM reservation 
             WHERE petId = :petId AND reservationDay >= :today and animalChecked=0"
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

                    $_SESSION['message'] = RESCRSUC;
                    if ($_SESSION['privilage'] == "Veterinarian") {
                        $sql = "SELECT u.userMail,u.usedLanguage,p.petName from user u inner join pet p ON u.userId=p.userId where p.petId=:petId";
                        $sql = $this->connection->prepare($sql);
                        $sql->bindValue(':petId', $selectedPetId);
                        $sql->execute();
                        $result = $sql->fetch();
                        if ($result['usedLanguage'] == "en") {
                            $pet_reservation = "made a reservation for yor pet named:";
                            $date = 'Date';
                            $time = 'Time';
                        } elseif ($result['usedLanguage'] == "hu") {
                            $pet_reservation = "időpontot foglalt a házikedvencednek:";
                            $date = 'Dátum';
                            $time = 'Időpont';
                        } else {
                            $pet_reservation = "je zakazao termin za pregled ljubimca:";
                            $date = 'Datum';
                            $time = "Vreme";
                        }

                        $_SESSION['reservationMail'] = $result['userMail'];
                        $_SESSION['petReservation'] = $_SESSION['firstName'] . " " . $_SESSION['lastName'] . " " . $pet_reservation . " " . $result['petName'];
                        $_SESSION['mailText'] = $date . " " . $reservationDate . "<br>" . $time . " " . $reservationStart . " - " . $reservationEnd;
                        header('Location:mail.php');
                        exit();
                    }
                } else {
                    $_SESSION['message'] = "You already have too many reservations for this pet.";
                }
            } else {
                $_SESSION['message'] = "All fields are required.";
            }
        }
        header('Location:book_apointment.php');

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
            $_SESSION['message'] = RESDELSUC;
        else
            $_SESSION['message'] = RESDELFAIL;
        header('Location:book_apointment.php');

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
            $payed=0;

            $sql2 = "SELECT * FROM product WHERE productId = :productId AND productName = :productName AND productCost = :productPrice";
            $stmt2 = $this->connection->prepare($sql2);
            $stmt2->bindParam(':productId', $productId, PDO::PARAM_STR);
            $stmt2->bindParam(':productName', $productName, PDO::PARAM_STR);
            $stmt2->bindParam(':productPrice', $productPrice, PDO::PARAM_STR);
            $stmt2->execute();

            if ($stmt2->rowCount() > 0) {

                $sql = "INSERT INTO user_product_relation( userId, productName,productPicture,productId,sum, price,productPayed,boughtDay) 
VALUES (:userId,:productName,:productPicture,:productId,:sum, :price,:productPayed,NOW())";
                $stmt = $this->connection->prepare($sql);
                $stmt->bindParam(':userId', $userId);
                $stmt->bindParam(':productName', $productName);
                $stmt->bindParam(':productPicture', $productPicture);
                $stmt->bindParam(':productId', $productId);
                $stmt->bindParam(':sum', $sum);
                $stmt->bindParam(':price', $productPrice);
                $stmt->bindParam(':productPayed', $payed);
                $stmt->execute();

            } else {
                $_SESSION['message'] = "Product ID, Name or Price does not match existing product.";
            }
        } else {
            $_SESSION['message'] = "Something is missing in the product parameters";
        }
        header('Location:products.php');
        exit();
    }

    public function addVet()
    {
        if (isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['tel']) && isset($_POST['mail'])) {

            $fname = $this->blurSwearWords($_POST['fname']);
            $lname = $this->blurSwearWords($_POST['lname']);
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
                    $_SESSION['workerLink'] = 'http://localhost/Humanz_Pets/resetPassword.php?verify_email=' . $mail . '&verification_code=' . $verification_code;
                    $_SESSION['message'] = "Worker added Successfully!";
                    $_SESSION['text'] = "<h2>Registration</h2>";
                    $_SESSION['verification_code'] = $verification_code;
                    $_SESSION['veterinarianEmail'] = $mail;
                    header('Location: mail.php');
                    exit();
                } else {
                    $_SESSION['message'] = ERROR;
                    header('Location: registration.php?token=' . $_SESSION['token']);
                    exit();
                }

            } catch (PDOException $e) {
                $_SESSION['message'] = ERROR . $e->getMessage();
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
                $petName = ucfirst(strtolower(trim($this->blurSwearWords($_POST["petName"]))));
                $bred = ucfirst(strtolower(trim($this->blurSwearWords($_POST["bred"]))));
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
                $petStmt = "INSERT INTO pet (petName, bred, petSpecies, profilePic, userId)
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
            $productName = ucfirst(strtolower(trim($this->blurSwearWords($_POST["productName"]))));
            $price = ucfirst(strtolower(trim($_POST["price"])));
$_SESSION['product']=true;
            $picture = $this->picture($_SESSION['backPic']);
            if ($picture == 4) {
                $picture = $_SESSION['updateProductPicture'];
            }
            unset($_SESSION['product']);
            $description = ucfirst(strtolower(trim($this->blurSwearWords($_POST["productDescription"]))));

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
        if (isset($_POST['productName']) && isset($_POST['price']) && isset($_POST['productDescription']) && isset($_POST['productLanguage'])&& isset($_SESSION['backPic'])) {
            try {
                $productName = ucfirst(strtolower(trim($this->blurSwearWords($_POST["productName"]))));
                $price = ucfirst(strtolower(trim($_POST["price"])));
                $productLanguage = $_POST["productLanguage"];
                $_SESSION['product']=true;
                $picture = $this->picture($_SESSION['backPic']);
                $description = ucfirst(strtolower(trim($this->blurSwearWords($_POST["productDescription"]))));
                unset($_SESSION['product']);
                // Insert the pet data into the database
                $stmt = "INSERT INTO product (productName, productCost, productPicture, description, productRelease,productLanguage)
                    VALUES (:productName, :price,:productPicture,:productDescription, NOW(),:productLanguage)";
                $query = $this->connection->prepare($stmt);
                $query->bindParam(':productName', $productName, PDO::PARAM_STR);
                $query->bindParam(':price', $price, PDO::PARAM_STR);
                $query->bindParam(':productPicture', $picture, PDO::PARAM_STR);
                $query->bindParam(':productDescription', $description, PDO::PARAM_STR);
                $query->bindParam(':productLanguage', $productLanguage, PDO::PARAM_STR);


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
        } else {
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

    public function modifyForAdmin(string $table, int $id)
    {
        $allowedTables = ['user', 'veterinarian'];
        if (!in_array($table, $allowedTables)) {
            throw new Exception("Invalid table name.");
        }

        // Correct query with dynamic table name
        $sql = "SELECT firstName, lastName, phoneNumber, {$table}Mail FROM {$table} WHERE {$table}Id = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) { // Ensure the result is not empty before outputting HTML
            $mailColumn = ($table === 'user') ? 'userMail' : 'veterinarianMail';
            echo '<input type="hidden" name="mail" class="form-control" id="mail" value="' . htmlspecialchars($result[$mailColumn]) . '">';
            echo '<input type="hidden" name="table" class="form-control" id="table" value="' . $table . '">';
            echo '  <div class="mb-3">
                <label for="knev" class="form-label">' . NAME . ':</label>
                <input type="text" class="form-control" placeholder="' . NAME . '" value="' . htmlspecialchars($this->blurSwearWords($result['firstName'])) . '" name="firstName" id="knev">
            </div>';

            echo ' <div class="mb-3">
                <label for="vnev" class="form-label">' . LASTNAME . ':</label>
                <input type="text" class="form-control" placeholder="' . LASTNAME . '" value="' . htmlspecialchars($this->blurSwearWords($result['lastName'])) . '" name="lastName" id="vnev">
            </div>';

            echo '<div class="mb-3">
                <label for="tel" class="form-label">' . PHONE . ':</label>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="' . PHONE . '" value="' . htmlspecialchars($result['phoneNumber']) . '" name="tel" id="tel2">
                </div>
            </div>';
        } else {
            echo "<p>No user found with the given ID.</p>";
        }
    }

    public function registration()
    {
        if (isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['tel']) && isset($_POST['mail']) && isset($_POST['pass']) && isset($_POST['pass2'])) {

            $_SESSION['registration'] = true;
            $fname = $this->blurSwearWords($_POST['fname']);
            $lname = $this->blurSwearWords($_POST['lname']);
            $tel = $_POST['tel'];

            $usedLanguage = $_POST['lang'];
            $mail = $_POST['mail'];
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
                            if ($rows['verification_time'] < $verification_time) {
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

                                    header('Location: registration.php');

                                    exit();
                                }
                            } else {
                                $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
                                $query = "UPDATE user SET verification_code = ? ,verification_time =? WHERE userMail = ?";
                                $query = $this->connection->prepare($query);
                                $query->execute([$verification_code, $verification_time, $mail]);
                                $_SESSION['message'] = "If you think the<b>E-mail</b> address is registered try again.";
                                $_SESSION['verification_code'] = $verification_code;
                                $_SESSION['email'] = $mail;
                                $_SESSION['registrationLink'] = 'http://localhost/Humanz_Pets/email-verification.php?verification_code=' . $verification_code;
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


                        if ($rows['veterinarianMail'] == $mail) {
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
                    $_SESSION['email'] = $mail;
                    $_SESSION['registrationLink'] = 'http://localhost/Humanz_Pets/email-verification.php?verification_code=' . $verification_code . '&verify_email=' . $mail;
                    header('Location: mail.php');
                    exit(); // Exit script after redirection
                } else {
                    $_SESSION['message'] = ERROR . $this->connection->error;
                    header('Location: registration.php');
                    exit();
                }


            } catch (Exception $e) {
                $_SESSION['message'] = ERROR . $e->getMessage();
                exit();
            }
        } else {
            $_SESSION['message'] = ERROR;
            exit();
        }

    }

    public function userModifyData($fname, $lname, $tel, $usedLanguage, $location)
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
        if ($usedLanguage != "SELECT_LANG") {
            if (strlen($tel) != 10 && strlen($tel) != 11) {
                $_SESSION['message'] = "You have to select a <b>Language</b>!";
                header('Location: ' . $location);
                exit();
            }
        }


    }

    public function modifyUser()
    {
        $count = 0;
        $phoneNumber = $_POST['tel'];
        if (isset($_POST['table'])) {
            // Check user privilege
            if ($_SESSION['privilage'] == 'Veterinarian' || $_POST['table'] == 'veterinarian') {
                $table = 'veterinarian';
                $sql = $this->connection->prepare("SELECT veterinarianID,firstName, lastName, phoneNumber,usedLanguage FROM veterinarian WHERE veterinarianMail = ?");
            } else {
                $table = 'user';
                $sql = $this->connection->prepare("SELECT userId,firstName, lastName, phoneNumber,usedLanguage privilage FROM user WHERE userMail = ?");
            }
// Prepare the initial query to retrieve existing user details

            $sql->execute([$_POST['mail']]);
            $result = $sql->fetch(PDO::FETCH_ASSOC);


// Calling userModifyData function with posted data
            if ($_POST['table'] == 'veterinarian') {
                $this->userModifyData($_POST['firstName'], $_POST['lastName'], $_POST['tel'], $_POST['usedLanguage'], "modify.php?veterinarianId=" . $result['veterinarianID']);
                $id = $result['veterinarianID'];
            } elseif ($_POST['table'] == 'user') {
                $this->userModifyData($_POST['firstName'], $_POST['lastName'], $_POST['tel'], $_POST['usedLanguage'], "modify.php?userId=" . $result['userId']);
                $id = $result['userId'];
            } else {
                $this->userModifyData($_POST['firstName'], $_POST['lastName'], $_POST['tel'], $_POST['usedLanguage'], "modify.php");
                $id = $_SESSION['userId'];
            }// Check if user data was found and update if necessary
            if ($result) {

                $empty = 0;
                // Update first name if provided
                if (!empty($_POST['firstName'])) {
                    $firstName = ucfirst(strtolower($this->blurSwearWords($_POST['firstName'])));
                    $sql = $this->connection->prepare("UPDATE $table SET firstName = ? WHERE " . $table . "Mail = ?");
                    $sql->execute([$firstName, $_POST['mail']]);
                    if ($_SESSION['email'] == $_POST['mail']) {
                        $_SESSION['firstName'] = $firstName;
                        $_SESSION['name'] = $_SESSION['firstName'] . " " . $_SESSION['lastName'];
                    }
                    $_SESSION['message'] = "First name is modified";
                    $count++;
                }


                // Update last name if provided
                if (!empty($_POST['lastName'])) {
                    $lastName = ucfirst(strtolower($this->blurSwearWords($_POST['lastName'])));
                    $sql = $this->connection->prepare("UPDATE $table SET lastName = ? WHERE " . $table . "Mail = ?");
                    $sql->execute([$lastName, $_POST['mail']]);
                    if ($_SESSION['email'] == $_POST['mail']) {
                        $_SESSION['lastName'] = $lastName;
                        $_SESSION['name'] = $_SESSION['firstName'] . " " . $_SESSION['lastName'];
                    }
                    $_SESSION['message'] = "Last name is modified";
                    $count++;
                }

                // Update phone number if provided
                if (!empty($_POST['tel'])) {
                    $sql = $this->connection->prepare("UPDATE $table SET phoneNumber = ? WHERE " . $table . "Mail = ?");
                    $sql->execute([$phoneNumber, $_POST['mail']]);

                    $_SESSION['message'] = "Phone number is modified";
                    $_SESSION['phone'] = $phoneNumber;
                    $count++;
                }
                if ($_POST['usedLanguage'] != "SELECT_LANG") {
                    $usedLanguage = $_POST['usedLanguage'];
                    $sql = $this->connection->prepare("UPDATE $table SET usedLanguage = ? WHERE " . $table . "Mail = ?");
                    $sql->execute([$usedLanguage, $_POST['mail']]);
                    $_SESSION['message'] = "Language is modified";
                    if ($_SESSION['email'] == $_POST['mail'])
                        $_SESSION['userLang'] = $usedLanguage;
                    $count++;
                }

            }

// Set session message based on whether any changes were made
            $_SESSION['message'] = $count > 0 ? "Changes saved" : "Empty data cannot be saved!";

            $stmt = "SELECT firstName, lastName, phoneNumber FROM $table WHERE " . $table . "Id = :Id";
            $stmt = $this->connection->prepare($stmt);
            $stmt->bindParam(':Id', $id);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $_SESSION['qrCodeFile'] = $this->createQrCode($row['firstName'] . ' ' . $row['lastName'], $row['phoneNumber']);
            }
            if ($_SESSION['privilage'] != 'veterinarian' && $_POST['table'] != 'veterinarian') {
                $stmt = "UPDATE qr_code qr
INNER JOIN $table u ON qr.userId = u.userId
SET qr.qrCodeName = :qrCodeName
WHERE u.userId = :userId";
                $stmt = $this->connection->prepare($stmt);
                $stmt->bindParam(':userId', $id);
                $stmt->bindParam(':qrCodeName', $_SESSION['qrCodeFile']);
                $stmt->execute();
            }
        } else {
            if ($_SESSION['privilage'] == 'Veterinarian') {
                $table = 'veterinarian';
                $sql = $this->connection->prepare("SELECT veterinarianID,firstName, lastName, phoneNumber,usedLanguage FROM veterinarian WHERE veterinarianMail = ?");
            } else {
                $table = 'user';
                $sql = $this->connection->prepare("SELECT userId,firstName, lastName, phoneNumber,usedLanguage privilage FROM user WHERE userMail = ?");
            }
// Prepare the initial query to retrieve existing user details

            $sql->execute([$_POST['mail']]);
            $result = $sql->fetch(PDO::FETCH_ASSOC);


// Calling userModifyData function with posted data
            if ($_SESSION['privilage'] == 'Veterinarian') {
                $this->userModifyData($_POST['firstName'], $_POST['lastName'], $_POST['tel'], $_POST['usedLanguage'], "modify.php?veterinarianId=" . $result['veterinarianID']);
                $id = $result['veterinarianID'];
            } elseif ($_POST['privilage'] == 'Guest') {
                $this->userModifyData($_POST['firstName'], $_POST['lastName'], $_POST['tel'], $_POST['usedLanguage'], "modify.php?userId=" . $result['userId']);
                $id = $result['userId'];
            } else {
                $this->userModifyData($_POST['firstName'], $_POST['lastName'], $_POST['tel'], $_POST['usedLanguage'], "modify.php");
                $id = $_SESSION['userId'];
            }// Check if user data was found and update if necessary
            if ($result) {

                $empty = 0;
                // Update first name if provided
                if (!empty($_POST['firstName'])) {
                    $firstName = ucfirst(strtolower($this->blurSwearWords($_POST['firstName'])));
                    $sql = $this->connection->prepare("UPDATE $table SET firstName = ? WHERE " . $table . "Mail = ?");
                    $sql->execute([$firstName, $_POST['mail']]);
                    if ($_SESSION['email'] == $_POST['mail']) {
                        $_SESSION['firstName'] = $firstName;
                        $_SESSION['name'] = $_SESSION['firstName'] . " " . $_SESSION['lastName'];
                    }
                    $_SESSION['message'] = "First name is modified";
                    $count++;
                }


                // Update last name if provided
                if (!empty($_POST['lastName'])) {
                    $lastName = ucfirst(strtolower($this->blurSwearWords($_POST['lastName'])));
                    $sql = $this->connection->prepare("UPDATE $table SET lastName = ? WHERE " . $table . "Mail = ?");
                    $sql->execute([$lastName, $_POST['mail']]);
                    if ($_SESSION['email'] == $_POST['mail']) {
                        $_SESSION['lastName'] = $lastName;
                        $_SESSION['name'] = $_SESSION['firstName'] . " " . $_SESSION['lastName'];
                    }
                    $_SESSION['message'] = "Last name is modified";
                    $count++;
                }

                // Update phone number if provided
                if (!empty($_POST['tel'])) {
                    $sql = $this->connection->prepare("UPDATE $table SET phoneNumber = ? WHERE " . $table . "Mail = ?");
                    $sql->execute([$phoneNumber, $_POST['mail']]);

                    $_SESSION['message'] = "Phone number is modified";
                    $_SESSION['phone'] = $phoneNumber;
                    $count++;
                }
                if ($_POST['usedLanguage'] != "SELECT_LANG") {
                    $usedLanguage = $_POST['usedLanguage'];
                    $sql = $this->connection->prepare("UPDATE $table SET usedLanguage = ? WHERE " . $table . "Mail = ?");
                    $sql->execute([$usedLanguage, $_POST['mail']]);
                    $_SESSION['message'] = "Language is modified";
                    if ($_SESSION['email'] == $_POST['mail'])
                        $_SESSION['userLang'] = $usedLanguage;
                    $count++;
                }

            }

// Set session message based on whether any changes were made
            $_SESSION['message'] = $count > 0 ? "Changes saved" : "Empty data cannot be saved!";

            $stmt = "SELECT firstName, lastName, phoneNumber FROM $table WHERE " . $table . "Id = :Id";
            $stmt = $this->connection->prepare($stmt);
            $stmt->bindParam(':Id', $id);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $_SESSION['qrCodeFile'] = $this->createQrCode($row['firstName'] . ' ' . $row['lastName'], $row['phoneNumber']);
            }
            if ($_SESSION['privilage'] != 'Veterinarian' && $_POST['table'] != 'veterinarian') {
                $stmt = "UPDATE qr_code qr
INNER JOIN $table u ON qr.userId = u.userId
SET qr.qrCodeName = :qrCodeName
WHERE u.userId = :userId";
                $stmt = $this->connection->prepare($stmt);
                $stmt->bindParam(':userId', $id);
                $stmt->bindParam(':qrCodeName', $_SESSION['qrCodeFile']);
                $stmt->execute();
            }
        }
// Redirect to index.php
        header('Location: index.php');
        exit();

    }

    public function picture($target = " ")
    {

        if (isset($_FILES['picture'])) {
            if(isset($_SESSION['product']))
            $target_dir = "pictures/products/";
            else
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
            $_SESSION['get_lang'] = $lang;
            $_SESSION['lang'] = $lang; // Save selected language in session
        } elseif (isset($_SESSION['get_lang'])) {
            $lang = $_SESSION['get_lang'];
            $_SESSION['lang'] = $lang;
        }elseif (isset($_COOKIE['userLang'])) {
            $lang = $_COOKIE['userLang'];
            $_SESSION['lang'] = $lang;
        }
        else {
            $lang = $_SESSION['userLang'] ?? 'en'; // Default to userLang or 'en'
            $_SESSION['lang'] = $lang; // Set session language to default
        }
        return $lang;
        // Include the language file
//            include "lang_$lang.php";

    }

    public function logOut()
    {
        // Clear all session data
        $_SESSION = [];
        session_unset();
        session_destroy();

        // Delete session cookies if set
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach ($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);

                // Check if the 'device_check' cookie exists before comparing
                if (!isset($_COOKIE['device_check']) || $name !== 'device_check') {
                    setcookie($name, '', time() - 3600, '/'); // Expire the cookie
                    unset($_COOKIE[$name]); // Remove from $_COOKIE array
                }
            }
        }


        // Force the page to reload and ensure no session data persists
        header('Location: index.php?refresh=1');
        exit();
    }



    public function fetchUserByEmail($email)
    {
        try {
            // Check the `user` table first
            $sql = "SELECT 'user' AS userType, userMail AS mail, userPassword AS password, banned AS banned,
                       firstName, lastName, profilePic, userId, phoneNumber, privilage, usedLanguage,verify,verification_code
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
         firstName, lastName, profilePic, veterinarianId, phoneNumber, usedLanguage,verify,verification_code
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
    public function checkForProxy() {
        $proxyHeaders = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_VIA',
            'HTTP_PROXY_CONNECTION'
        ];

        foreach ($proxyHeaders as $header) {
            if (isset($_SERVER[$header])) {
                return 'Proxy Detected';
            }
        }

        return 'No Proxy';
    }
    public function getISPFromIP($ip_address) {
        $access_key = 'your_access_key';
        $url = "http://ipinfo.io/{$ip_address}/json?token={$access_key}";

        // Suppress errors and check if the URL can be fetched successfully
        $response = @file_get_contents($url);
        if ($response === FALSE) {
            return 'Unknown ISP';
        }

        $data = json_decode($response, true);
        return isset($data['org']) ? $data['org'] : 'Unknown ISP';  // 'org' contains ISP info
    }

    public function getCountryFromIP($ip_address) {
        $access_key = 'your_access_key';
        $url = "http://ipinfo.io/{$ip_address}/json?token={$access_key}";

        // Suppress errors and check if the URL can be fetched successfully
        $response = @file_get_contents($url);
        if ($response === FALSE) {
            return 'Unknown';
        }

        $data = json_decode($response, true);
        return isset($data['country']) ? $data['country'] : 'Unknown';
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
                        if ($result['verify'] == 1) {
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
                                if (isset($_SESSION['registration']))
                                    unset($_SESSION['registration']);
                                setcookie("last_activity", time(), time() + 10 * 60, "/");
                                if (!isset($_COOKIE['device_check'])) {
                                    // Set the device_check cookie for 24 hours
                                    setcookie("device_check", time(), time() + 24 * 60 * 60, "/");

                                    // Capture the user agent
                                    $user_agent = $_SERVER['HTTP_USER_AGENT'];

                                    // Capture the IP address
                                    $ip_address = $_SERVER['REMOTE_ADDR'];

                                    // Get the country (you need to implement this or use a service like ipinfo.io)
                                    $country = $this->getCountryFromIP($ip_address);  // Use the function from earlier

                                    // Capture the current date and time
                                    $date_time = date("Y-m-d H:i:s");

                                    // Detect device type
                                    if (strpos($user_agent, 'Mobile') !== false) {
                                        $device_type = 'Mobile';
                                    } elseif (strpos($user_agent, 'Tablet') !== false) {
                                        $device_type = 'Tablet';
                                    } else {
                                        $device_type = 'Desktop';
                                    }

                                    // Check if the IP is using a proxy
                                    $proxy = $this->checkForProxy();

                                    // Get ISP information (using ipinfo.io or another service)
                                    $isp = $this->getISPFromIP($ip_address);  // Use the function from earlier

                                    // Database connection (Assume $this->connection is your DB connection)
                                    $sql = "INSERT INTO log (user_agent, ip_address, country, date_time, device_type, proxy, isp) 
            VALUES (:user_agent, :ip_address, :country, :date_time, :device_type, :proxy, :isp)";

                                    $stmt = $this->connection->prepare($sql);
                                    $stmt->execute([
                                        ':user_agent' => $user_agent,
                                        ':ip_address' => $ip_address,
                                        ':country' => $country,
                                        ':date_time' => $date_time,
                                        ':device_type' => $device_type,
                                        ':proxy' => $proxy,
                                        ':isp' => $isp
                                    ]);
                                } else {
                                    // Check if the cookie has expired (older than 24 hours)
                                    if (time() - $_COOKIE['device_check'] > 24 * 60 * 60) {
                                        // Cookie has expired, reset it
                                        setcookie("device_check", time(), time() + 24 * 60 * 60, "/");
                                    }
                                }



                                header('Location: index.php?refresh=1');
                                exit();
                            }
                        } else {
                            $this->errorLogInsert($mail, "The password was not valid!", "Log in", "Wrong password!");
                            $_SESSION['message'] = "Verify Account, we sent a mail to you!";
                            $_SESSION['text'] = "<h2>Registration</h2>";
                            $_SESSION['email'] = $mail;
                            $_SESSION['registrationLink'] = 'http://localhost/Humanz_Pets/email-verification.php?verification_code=' . $result['verification_code'] . '&verify_email=' . $mail;
                            $_SESSION['reSend'] = true;
                            header('Location: mail.php');

                            exit();
                        }
                    } else {
                        $this->errorLogInsert($mail, "The password was not valid!", "Log in", "Wrong password!");
                        $_SESSION['message'] = "Wrong password!";
                    }
                }
            } else {
                $_SESSION['message'] = EMAILNOTREG;
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
            if (isset($_COOKIE['last_activity']) && isset($_SESSION['email']) && !isset($_SESSION['registration'])) {
                $result = $this->fetchUserByEmail($_SESSION['email']);

                if ($result['banned']) {
                    $_SESSION = [];
                    session_unset();
                    session_destroy();
                    if (isset($_SERVER['HTTP_COOKIE'])) {
                        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                        foreach ($cookies as $cookie) {
                            $parts = explode('=', $cookie);
                            $name = trim($parts[0]);

                            // Check if the 'device_check' cookie exists before comparing
                            if (!isset($_COOKIE['device_check']) || $name !== 'device_check') {
                                setcookie($name, '', time() - 3600, '/'); // Expire the cookie
                                unset($_COOKIE[$name]); // Remove from $_COOKIE array
                            }
                        }
                    }


                    // Redirect to login page
                    header('Location: index.php');
                    exit();
                }
                if (isset($_SESSION['backPic']))
                    $backPage = $_SESSION['backPic'];
                $mail = $_SESSION['email'];
                if ($_SESSION['privilage'] == 'User') {
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
                unset($_COOKIE['userLang']);

                setcookie("email", $_SESSION['email'], time() + 10 * 60, "/");
                setcookie("name", $_SESSION['name'], time() + 10 * 60, "/");
                setcookie("profilePic", $_SESSION['profilePic'], time() + 10 * 60, "/");
                setcookie("userId", $_SESSION['userId'], time() + 10 * 60, "/");
                setcookie("phone", $_SESSION['phone'], time() + 10 * 60, "/");
                setcookie("privilage", $_SESSION['privilage'], time() + 10 * 60, "/");
                setcookie("userLang",$_SESSION['userLang'] ,  time() + 10 * 60, "/");
                setcookie("last_activity", time(), time() + 10 * 60, "/");

                if ($_SESSION['privilage'] == 'User' || $_SESSION['privilage'] == 'Admin') {

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
                        if ($result == 0 && $_SESSION['privilage'] != 'Admin') {
                            $_SESSION['message'] = '<br>You have to <b>chose a veterinarian</b> to use this account further!<br><br><a href="functions.php?action=logOut">Log out</a>';
                            if ($currentPage != 'selectVeterinarian.php' && $currentPage != 'registerAnimal.php') {
                                header('Location: selectVeterinarian.php');
                                exit();
                            }
                        }
                    }
                    $sql = "SELECT p.petId FROM pet p INNER JOIN user u ON p.userId = u.userId WHERE u.userMail = :mail AND p.veterinarID IS NULL";
                    $stmt = $this->connection->prepare($sql);
                    $stmt->bindValue(":mail", $_SESSION['email']);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (!empty($result) && $currentPage != "selectVeterinarian.php" && $currentPage != "updateAnimal.php") { // Check if there are any results
                        $_SESSION['message'] = '<br>You have to <b>choose a veterinarian</b> to use this account further!<br><br><a href="functions.php?action=logOut">Log out</a>';
                        header('Location: selectVeterinarian.php');
                        exit();
                    } elseif (!empty($result) && $currentPage == "selectVeterinarian.php")
                        $_SESSION['message'] = '<br>You have to <b>choose a veterinarian</b> to use this account further!<br><br><a href="functions.php?action=logOut">Log out</a>';

                }

            } elseif (isset($_COOKIE['email']) && !isset($_SESSION['registration'])) {
                $result = $this->fetchUserByEmail($_COOKIE['email']);
                if ($result['banned']) {
                    $_SESSION = [];
                    session_unset();
                    session_destroy();
                    if (isset($_SERVER['HTTP_COOKIE'])) {
                        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                        foreach ($cookies as $cookie) {
                            $parts = explode('=', $cookie);
                            $name = trim($parts[0]);

                            // Check if the 'device_check' cookie exists before comparing
                            if (!isset($_COOKIE['device_check']) || $name !== 'device_check') {
                                setcookie($name, '', time() - 3600, '/'); // Expire the cookie
                                unset($_COOKIE[$name]); // Remove from $_COOKIE array
                            }
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
                $_SESSION['userLang'] = $_COOKIE['userLang'];

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
                unset($_COOKIE['userLang']);

                setcookie("email", $_SESSION['email'], time() + 10 * 60, "/");
                setcookie("name", $_SESSION['name'], time() + 10 * 60, "/");
                setcookie("profilePic", $_SESSION['profilePic'], time() + 10 * 60, "/");
                setcookie("userId", $_SESSION['userId'], time() + 10 * 60, "/");
                setcookie("phone", $_SESSION['phone'], time() + 10 * 60, "/");
                setcookie("privilage", $_SESSION['privilage'], time() + 10 * 60, "/");
                setcookie("userLang",$_SESSION['userLang'] ,  time() + 10 * 60, "/");
                setcookie("last_activity", time(), time() + 10 * 60, "/");

                if ($_SESSION['privilage'] == 'User' || $_SESSION['privilage'] == 'Admin') {

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
                        if ($result == 0 && $_SESSION['privilage'] != 'Admin') {
                            $_SESSION['message'] = '<br>You have to <b>chose a veterinarian</b> to use this account further!<br><br><a href="functions.php?action=logOut">Log out</a>';
                            if ($currentPage != 'selectVeterinarian.php' && $currentPage != 'registerAnimal.php' && $currentPage != 'updateAnimal.php') {
                                header('Location: selectVeterinarian.php');
                                exit();
                            }
                        }
                    }
                    $sql = "SELECT p.petId FROM pet p INNER JOIN user u ON p.userId = u.userId WHERE u.userMail = :mail AND p.veterinarID IS NULL";
                    $stmt = $this->connection->prepare($sql);
                    $stmt->bindValue(":mail", $_SESSION['email']);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (!empty($result) && $currentPage != "selectVeterinarian.php" && $currentPage != "updateAnimal.php") { // Check if there are any results
                        $_SESSION['message'] = '<br>You have to <b>choose a veterinarian</b> to use this account further!<br><br><a href="functions.php?action=logOut">Log out</a>';
                        header('Location: selectVeterinarian.php');
                        exit();
                    } elseif (!empty($result) && $currentPage == "selectVeterinarian.php")
                        $_SESSION['message'] = '<br>You have to <b>choose a veterinarian</b> to use this account further!<br><br><a href="functions.php?action=logOut">Log out</a>';


                }
                header('Location: index.php');
                exit();

            } elseif (isset($_SESSION['email'])) {
                session_unset();
                session_destroy();
                header('Location: logIn.php?refresh=1');
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
