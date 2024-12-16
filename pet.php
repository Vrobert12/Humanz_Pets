<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    $lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
    if(isset($_GET['lang'])){
        $_SESSION['lang'] = $_GET['lang'];
    }
    include "lang_$lang.php";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Makes it responsive -->
    <title>User Data</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Optional: Custom Styles -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="mainForm">
    <a class="btn btn-secondary" href="index.php"><?php echo BACK?></a><br><br>
    <?php
    include "functions.php";
    $functions=new Functions();
    $functions->checkAutoLogin();
    if (isset($_SESSION['email']) && isset($_GET['email'])) {
        $userID = $_SESSION['userId'];
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

                echo '<tr><td>'.INFO.'</td></tr>';

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


                echo '<tr><td>'.NAME.':' .$petName.'</td></tr>';
                echo '<tr><td>'.BREED.':'.$typeOfAnimal.'</td></tr>';
                echo '<tr><td>'.SPECIES.':' .$petSpecies.'</td></tr>';
                echo '<tr><td>'.EMAIL.':'.$userMail.'</td></tr>';

                echo "</table>";
            }
        else
            echo '<p>'.NOPET.'</p>';



    } else {
        header('Location: index.php');
        exit();
    }
    ?>

</div>

</body>
</html>
