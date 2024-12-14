<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Data</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="mainForm">
    <a href="index.php" class="nextPage">Back to index page</a>
    <?php
    include "functions.php";
    $autoload=new Functions();
    $autoload->checkAutoLogin();
    if (isset($_SESSION['email']) && isset($_GET['email'])) {
        $userID = $_SESSION['userId'];
        $functions = new Functions();
        // Use the connect method from the Functions class
        $connection = $functions->connect($GLOBALS['dsn'], PARAMS['USER'], PARAMS['PASSWORD'], $GLOBALS['pdoOptions']);


        $sql = "SELECT qrCodeName FROM qr_code  where userId= :userId";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(":userId", $userID, PDO::PARAM_INT);
        if($stmt->execute())
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $qrPicture = htmlspecialchars($row['qrCodeName']);
                echo "<table class='profile-table'>";
                echo "<tr><td rowspan='4' ><img alt='Profile Picture' style='border-radius: 0'width='200' height='200' src='$qrPicture'></td></tr>";

                echo "<tr><td>Your contac informations if are in the QrCode,<br> you can use it to put on your pets collar.</td></tr>";

                echo "</table>";
            }

        $sql = "SELECT p.petName, p.bred, p.petSpecies, u.userMail, p.petPicture FROM  user u 
    inner join pet p on u.userId =p.userId where u.userId= :userId";

        $stmt = $connection->prepare($sql);
        $stmt->bindParam(":userId", $userID, PDO::PARAM_INT);
        if($stmt->execute())
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<table class='profile-table'>";
                $petName = htmlspecialchars($row['petName']);
                $typeOfAnimal = htmlspecialchars($row['bred']);
                $petSpecies = htmlspecialchars($row['petSpecies']);
                $userMail = htmlspecialchars($row['userMail']);
                $petPicture = htmlspecialchars($row['petPicture']);

                echo "<tr><td rowspan='6' style='padding: 20px; text-align: center;'>
            <img alt='Profile Picture' width='400' height='300' src='pictures/$petPicture'>
        </td></tr>";


                echo "<tr><td>Pet Name: $petName</td></tr>";
                echo "<tr><td>Brad: $typeOfAnimal</td></tr>";
                echo "<tr><td>Species: $petSpecies</td></tr>";
                echo "<tr><td>Email: $userMail</td></tr>";

                echo "</table>";
            }
        else
            echo "<p>You do not have access pets registered to your account</p>";



    } else {
        header('Location: index.php');
        exit();
    }
    ?>

</div>

</body>
</html>
