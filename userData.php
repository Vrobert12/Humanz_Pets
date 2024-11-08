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
session_start();

include "functions.php";
if(isset($_SESSION['message'])){
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}
$functions = new Functions();

if (isset($_SESSION['email']) && isset($_GET['email'])) {
    $userID = $_SESSION['userID'];

    // Use the connect method from the Functions class
    $connection = $functions->connect($GLOBALS['dsn'], PARAMS['USER'], PARAMS['PASSWORD'], $GLOBALS['pdoOptions']);

    $sql = "SELECT FirstName, LastName, phoneNumber, userMail, profilePic, privilage, registrationTime 
            FROM `user` WHERE userID = :userId";

    $stmt = $connection->prepare($sql);
    $stmt->bindParam(":userId", $userID, PDO::PARAM_INT);
    $stmt->execute();

    echo "<table class='profile-table'>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $firstName = htmlspecialchars($row['FirstName']);
        $lastName = htmlspecialchars($row['LastName']);
        $phoneNumber = htmlspecialchars($row['phoneNumber']);
        $userMail = htmlspecialchars($row['userMail']);
        $profilePic = htmlspecialchars($row['profilePic']);
        $privilage = htmlspecialchars($row['privilage']);
        $registrationTime = htmlspecialchars($row['registrationTime']);

        // Profile picture and name in the first row
        echo "<tr><td rowspan='6' style='padding: 20px; text-align: center;'>
            <img alt='Profile Picture' src='pictures/$profilePic'>
        </td></tr>";

        // Add user details
        echo "<tr><td>Name: $firstName $lastName</td></tr>";
        echo "<tr><td>Phone Number: $phoneNumber</td></tr>";
        echo "<tr><td>Email: $userMail</td></tr>";
        echo "<tr><td>Privilege: $privilage</td></tr>";
        echo "<tr><td>Registration Time: $registrationTime</td></tr>";
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
