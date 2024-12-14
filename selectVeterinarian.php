<?php
session_start();
include "functions.php";
$autoload=new Functions();
$autoload->checkAutoLogin('selectVeterinarian.php');
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
include_once "lang_$lang.php";
$_SESSION['backPic'] = "selectVeterinarian.php";
$functions = new Functions();
$connection = $functions->connect($GLOBALS['dsn'], PARAMS['USER'], PARAMS['PASSWORD'], $GLOBALS['pdoOptions']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <script src="indexJS.js"></script>
    <style>
        .warning {
            color: red;
        }
        .inputok {
            border-radius: 10px;
            font-size: 20px;
            padding: 10px;
            margin: 10px;
            text-align: center;
        }
        .inputok.error {
            border-color: red;
        }
    </style>

</head>
<body>

<?php

if (isset($_SESSION['token']) && isset($_GET['token'])) {
    if ($_SESSION['token'] != $_GET['token']) {
        header('location:' . $_SESSION['previousPage']);
        $_SESSION['title'] = "";
        exit();
    } else {
        $_SESSION['token'] = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
    }
} else {
    echo '<form method="post" action="functions.php" class="mainForm" enctype="multipart/form-data">';
}
if (isset($_SESSION['title'])) {
    echo "<h2 style='color: #2a7e2a'>" . $_SESSION['title'] . "</h2>";
}
$_SESSION['backPic']='selectVeterinarian.php';
if(isset($_SESSION['message'])) {
    echo "<p class='warning'>" .$_SESSION['message']."</p>";
    unset($_SESSION['message']);
}
$userID = $_SESSION['userId'];
$functions = new Functions();
// Use the connect method from the Functions class
$connection = $functions->connect($GLOBALS['dsn'], PARAMS['USER'], PARAMS['PASSWORD'], $GLOBALS['pdoOptions']);



$sql = "SELECT p.petId,p.petName, p.bred, p.petSpecies, u.userMail, p.petPicture FROM user u
    inner join pet p on u.userId =p.userId where u.userId= :userId and veterinarId is NULL";

$stmt = $connection->prepare($sql);
$stmt->bindParam(":userId", $userID, PDO::PARAM_INT);
$stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<h1 style='color: #2a7e2a'>Select your veterinar for " . $row['petName'] . "</h1>";
        echo "<table class='profile-table'>";
        $_SESSION['petId'] = $row['petId'];
        echo "<tr><td rowspan='6' style='padding: 20px; text-align: center;'>
            <img alt='Profile Picture' width='200' height='200' src='pictures/" . $row['petPicture'] . "'>
        </td></tr>";
        echo "<tr><td>Pet Name: " . $row['petName'] . "</td></tr>";
        echo "<tr><td>Brad:" . $row['bred'] . "</td></tr>";
        echo "<tr><td>Species: " . $row['petSpecies'] . "</td></tr>";


        echo "</table>";
    }


echo "<h1 style='color: #2a7e2a'>List of our veterinarians </h1>";
$sql="SELECT * FROM veterinarian";
$stmt = $connection->prepare($sql);
$stmt->execute();
if($stmt->rowCount() > 0){
    echo "<table class='profile-table'>";
    foreach($stmt->fetchAll() as $row){
        echo "<tr><td rowspan='6' style='padding: 20px; text-align: center;'>
            <img alt='Profile Picture' width='200' height='200' src='pictures/".$row['profilePic']."'>
        </td></tr>";
        echo "<tr><td>".$row['firstName']."</td></tr>";
        echo "<tr><td>".$row['lastName']."</td></tr>";
        echo "<tr><td>".$row['veterinarianMail']."</td></tr>";
        echo "<tr><td>".$row['phoneNumber']."</td></tr>";
        echo "<tr><td><input type=submit value='Chose Veterinarian'></td></tr>";
        echo "<input type=hidden  name='action' value='veterinarianChose'>";
        $_SESSION['veterinarianId']=$row['veterinarianId'];

    }
    echo "</table>";
}




?>
</form>

</body>
</html>
