<?php

require 'vendor/autoload.php';

include "functions.php";
$functions=new Functions();
$lang=$functions->language();
$functions->checkAutoLogin();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Data</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-image {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 10%; /* Slightly rounded corners */
        }
        .profile-section {
            margin-bottom: 20px;
        }
    </style>
</head>
<body style="background: #659df7">
<div class="container mt-5">
    <a class="btn btn-secondary mb-4" href="index.php"><?php echo BACK ?></a>
    <?php

    if (isset($_SESSION['email']) && isset($_GET['email'])) {
        $userID = $_SESSION['userId'];
        $connection = $functions->connect($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $pdoOptions);

        echo '<div class="row flex-column-reverse flex-lg-row">';
$_SESSION['backPic']="pet.php";
        // Fetch pet details
        echo '<div class="col-lg-8 order-lg-2 profile-section">';
        $sql = "SELECT p.petId,p.petName, p.bred, p.petSpecies, u.userMail, p.petPicture FROM user u 
                INNER JOIN pet p ON u.userId = p.userId WHERE u.userId = :userId";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(":userId", $userID, PDO::PARAM_INT);
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $petName = htmlspecialchars($row['petName']);
                $typeOfAnimal = htmlspecialchars($row['bred']);
                $petSpecies = htmlspecialchars($row['petSpecies']);
                $userMail = htmlspecialchars($row['userMail']);
                $petPicture = htmlspecialchars($row['petPicture']);
                $_SESSION['petId'] = $row['petId'];
                echo '<div class="row mb-4">';
                echo '<div class="col-md-4 text-center"><img class="profile-image" alt="Pet Picture" src="pictures/' . $petPicture . '"></div>';
                echo '<div class="col-md-8">';
                echo '<br><p><strong>' . NAME . ':</strong> ' . $petName . '</p>';
                echo '<p><strong>' . BREED . ':</strong> ' . $typeOfAnimal . '</p>';
                echo '<p><strong>' . SPECIES . ':</strong> ' . $petSpecies . '</p>';
                echo '<p><strong>' . EMAIL . ':</strong> ' . $userMail . '</p>';

                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>' . NOPET . '</p>';
        }
        echo '</div>';

        // Fetch QR codes
        echo '<div class="col-lg-4 order-lg-1 profile-section">';
        $sql = "SELECT qrCodeName FROM qr_code WHERE userId= :userId";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(":userId", $userID, PDO::PARAM_INT);
        if ($stmt->execute()) {
            echo '<h5 class="text-center"> '.QR.'</h5>';
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $qrPicture = htmlspecialchars($row['qrCodeName']);
                echo '<div class="mb-4 text-center">';
                echo '<img class="profile-image" alt="QR Code" src="' . $qrPicture . '">';
                echo '<p>' . INFO . '</p>';
                echo ' <form action="generate_pdf.php" method="POST">
        <input type="hidden" name="qrImage" value="'.$qrPicture.'">

        <button type="submit" class="btn btn-primary">'.GENPDF.'</button>
    </form>
</div>';
                echo '</div>';
            }
        }
        echo '</div>';

        echo '</div>';
    } else {
        header('Location: index.php');
        exit();
    }
    ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
