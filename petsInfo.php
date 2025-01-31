<?php

require 'vendor/autoload.php';

include "functions.php";
$functions = new Functions();
$lang = $functions->language();
include "lang_$lang.php";
$functions->checkAutoLogin();
if ($_SESSION['privilage'] != "Veterinarian") {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-image {
            width: 100%;
            max-width: 150px;
            height: 150px;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body style="background: #659df7">
<div class="container mt-5">
    <a class="btn btn-secondary mb-4" href="<?php echo $_SESSION['backPic']; ?>"><?php echo BACK; ?></a>

    <?php
    if (isset($_SESSION['email'])) {
        $vetID = $_SESSION['userId'];
        $connection = $functions->connect($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $pdoOptions);

        echo '<div class="row">';
        echo '<div class="col-lg-8 mx-auto">';

        $query = "SELECT u.firstName, u.lastName, u.userMail, u.phoneNumber, p.petId, p.petName, p.bred, p.petSpecies, p.petPicture, r.reservationDay, r.reservationTime, r.period 
                  FROM veterinarian v 
                  INNER JOIN pet p ON v.veterinarianId = p.veterinarId 
                  INNER JOIN reservation r ON r.petId = p.petId 
                  INNER JOIN user u ON p.userId = u.userId 
                  WHERE r.veterinarianId = :vetID AND reservationDay >= CURDATE()";

        if (isset($_GET['petId'])) {
            $query .= " AND p.petId = :petID";
        }

        $stmt = $connection->prepare($query);
        $stmt->bindParam(":vetID", $vetID, PDO::PARAM_INT);
        if (isset($_GET['petId'])) {
            $stmt->bindParam(":petID", $_GET['petId'], PDO::PARAM_INT);
        }

        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="row mb-4 p-3 bg-light rounded shadow-sm">';
                echo '<div class="col-md-4 text-center">';
                echo '<img class="profile-image" alt="Pet Picture" src="pictures/' . htmlspecialchars($row['petPicture']) . '">';
                echo '</div>';
                echo '<div class="col-md-8">';
                echo '<h5>' . htmlspecialchars($row['petName']) . '</h5>';
                echo '<p><strong>' . BREED . ':</strong> ' . htmlspecialchars($row['bred']) . '</p>';
                echo '<p><strong>' . SPECIES . ':</strong> ' . htmlspecialchars($row['petSpecies']) . '</p>';
                echo '<p><strong>' . EMAIL . ':</strong> ' . htmlspecialchars($row['userMail']) . '</p>';
                echo '<p><strong>' . NAME . ':</strong> ' . htmlspecialchars($row['firstName']) . ' ' . htmlspecialchars($row['lastName']) . '</p>';
                echo '<p><strong>' . PHONE . ':</strong> ' . htmlspecialchars($row['phoneNumber']) . '</p>';
                echo '<p><strong>' . RESERVATION3 . ':</strong> ' . htmlspecialchars($row['reservationDay']) . '</p>';
                echo '<p><strong>' . RESERVATION . ':</strong> ' . htmlspecialchars($row['reservationTime']) . ' <strong>' . RESERVATION2 . ':</strong> ' . htmlspecialchars($row['period']) . '</p>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>' . NOPET . '</p>';
        }
        echo '</div>';
        echo '</div>';
    } else {
        header('Location: index.php');
        exit();
    }
    ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
