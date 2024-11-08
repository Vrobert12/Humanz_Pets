<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Data</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="mainForm">

<?php
include 'functions.php';
if (isset($_SESSION['email']) && isset($_GET['email'])) {
    $userID = $_SESSION['userID'];
$functions = new Functions();
    // Use the connect method from the Functions class
    $connection = $functions->connect($GLOBALS['dsn'], PARAMS['USER'], PARAMS['PASSWORD'], $GLOBALS['pdoOptions']);

    $sql = "SELECT p.petName, p.typeOfAnimal, p.petSpecies, u.userMail, u.profilePic FROM pet p inner join pet_user_relation pu 
    on p.petId=pu.petId INNER join user u on pu.userId=u.userId  where u.userId= :userId";

    $stmt = $connection->prepare($sql);
    $stmt->bindParam(":userId", $userID, PDO::PARAM_INT);
    $stmt->execute();

    echo "<table class='profile-table'>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $petName = htmlspecialchars($row['petName']);
        $typeOfAnimal = htmlspecialchars($row['typeOfAnimal']);
        $petSpecies = htmlspecialchars($row['petSpecies']);
        $userMail = htmlspecialchars($row['userMail']);
        $profilePic = htmlspecialchars($row['profilePic']);


        // Profile picture and name in the first row
        echo "<tr><td rowspan='6' style='padding: 20px; text-align: center;'>
            <img alt='Profile Picture' src='pictures/$profilePic'>
        </td></tr>";

        // Add user details
        echo "<tr><td>Pet Name: $petName</td></tr>";
        echo "<tr><td>Brad: $typeOfAnimal</td></tr>";
        echo "<tr><td>Species: $petSpecies</td></tr>";
        echo "<tr><td>Email: $userMail</td></tr>";


    }

    echo "</table>";
} else {
    header('Location: index.php');
    exit();
}
?>
    <a href="index.php" class="nextPage">Back to index page</a>
</div>
</body>
</html>
