<?php
session_start();

include "functions.php";

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
    echo "<table style='border: 1px solid black; border-collapse: collapse;'>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $firstName = htmlspecialchars($row['FirstName']);
        $lastName = htmlspecialchars($row['LastName']);
        $phoneNumber = htmlspecialchars($row['phoneNumber']);
        $userMail = htmlspecialchars($row['userMail']);
        $profilePic = htmlspecialchars($row['profilePic']);
        $privilage = htmlspecialchars($row['privilage']);
        $registrationTime = htmlspecialchars($row['registrationTime']);

        // Add profile picture in the first row with a span of 6
        echo "<tr><td rowspan='6' style='padding: 10px;'>
            <img alt='img' width='80' height='80' class='rounded-circle me-2' src='pictures/$profilePic'>
          </td></tr>";
        // Add each data row
        echo "<tr><td style='padding: 5px;'>Name: $firstName $lastName</td></tr>";
        echo "<tr><td style='padding: 5px;'>Phone Number: $phoneNumber</td></tr>";
        echo "<tr><td style='padding: 5px;'>Email: $userMail</td></tr>";
        echo "<tr><td style='padding: 5px;'>Privilege: $privilage</td></tr>";
        echo "<tr><td style='padding: 5px;'>Registration Time: $registrationTime</td></tr>";
    }

    echo "</table>";

} else {
    header('Location: index.php');
    exit();
}
?>
