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
    <script src="search.js"></script>
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
    <div class="d-flex flex-wrap justify-content-center">
        <div class="users">
            <form id="searchForm" method="post">
                <input type="text" id="search" name="search" placeholder="Pet Name" oninput="performSearch('pet.php?email=<?php echo $_SESSION['email']; ?>')">
                <input type="hidden" name="searchAction" value="1">
            </form>

        </div>
    </div>
    <?php

    if (isset($_SESSION['email']) && isset($_GET['email'])) {
        $userID = $_SESSION['userId'];
        $connection = $functions->connect($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $pdoOptions);


        if (isset($_POST['search']) && !empty($_POST['search'])) {
            $searchTerm = "%" . $_POST['search'] . "%";

            // Assuming you already have a database connection available
            $connection = $functions->connect($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $pdoOptions);

            // Search for pets by name
            $stmt = $connection->prepare("SELECT p.petId, p.petName, p.bred, p.petSpecies, u.userMail, p.profilePic 
                                  FROM user u 
                                  INNER JOIN pet p ON u.userId = p.userId 
                                  WHERE p.petName LIKE :searchTerm AND p.userId=:userId");
            $stmt->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
            $stmt->execute();
            $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($pets)) {
                // Display found pets
                foreach ($pets as $row) {
                    $petName = htmlspecialchars($row['petName']);
                    $typeOfAnimal = htmlspecialchars($row['bred']);
                    $petSpecies = htmlspecialchars($row['petSpecies']);
                    $userMail = htmlspecialchars($row['userMail']);
                    $petPicture = htmlspecialchars($row['profilePic']);

                    echo '<div class="row mb-4">';
                    echo '<div class="col-md-4 text-center"><img class="profile-image" alt="Pet Picture" src="pictures/' . $petPicture . '"></div>';
                    echo '<div class="col-md-8">';
                    echo '<br><p><strong>' . NAME . ':</strong> ' . $petName . '</p>';
                    echo '<p><strong>' . BREED . ':</strong> ' . $typeOfAnimal . '</p>';
                    echo '<p><strong>' . SPECIES . ':</strong> ' . $petSpecies . '</p>';
                    echo '<p><strong>' . EMAIL . ':</strong> ' . $userMail . '</p>';
                    echo "<form action='updateAnimal.php' method='get'>
                    <input type='hidden' name='petId' value='" . $row['petId'] . "'>
                    <input type='hidden' name='petName' value='" . $row['petName'] . "'>
                    <input type='hidden' name='bred' value='" . $row['bred'] . "'>
                    <input type='hidden' name='petSpecies' value='" . $row['petSpecies'] . "'>
                    <input type='hidden' name='petPicture' value='" . $row['profilePic'] . "'>
                    <input type='submit' class='btn btn-success' value='" . UPDATE_PET . "'>
                </form>";
                    echo '</div>';
                    echo '</div>';
                }
            }
        }
else{
    echo '<div id="list" class="d-flex flex-wrap justify-content-center">';
        echo '<div class="row flex-column-reverse flex-lg-row">';
$_SESSION['backPic']="pet.php";
        // Fetch pet details
        echo '<div class="col-lg-8 order-lg-2 profile-section">';
        $sql = "SELECT p.petId,p.petName, p.bred, p.petSpecies, u.userMail, p.profilePic FROM user u 
                INNER JOIN pet p ON u.userId = p.userId WHERE u.userId = :userId";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(":userId", $userID, PDO::PARAM_INT);
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $petName = htmlspecialchars($row['petName']);
                $typeOfAnimal = htmlspecialchars($row['bred']);
                $petSpecies = htmlspecialchars($row['petSpecies']);
                $userMail = htmlspecialchars($row['userMail']);
                $petPicture = htmlspecialchars($row['profilePic']);
                $_SESSION['petId'] = $row['petId'];
                echo '<div class="row mb-4">';
                echo '<div class="col-md-4 text-center"><img class="profile-image" alt="Pet Picture" src="pictures/' . $petPicture . '"></div>';
                echo '<div class="col-md-8">';
                echo '<br><p><strong>' . NAME . ':</strong> ' . $petName . '</p>';
                echo '<p><strong>' . BREED . ':</strong> ' . $typeOfAnimal . '</p>';
                echo '<p><strong>' . SPECIES . ':</strong> ' . $petSpecies . '</p>';
                echo '<p><strong>' . EMAIL . ':</strong> ' . $userMail . '</p>';
                echo "<form action='updateAnimal.php'class='mainForm' method='get'>";
                echo "<input type='hidden' name='petId' value='" . $row['petId'] . "'>
                <input type='hidden' name='petName' value='" . $row['petName'] . "'>
<input type='hidden' name='bred' value='" . $row['bred'] . "'>
<input type='hidden' name='petSpecies' value='" . $row['petSpecies'] . "'>
<input type='hidden' name='petPicture' value='" . $row['profilePic'] . "'>";
                $_SESSION['petId'] = $row['petId'];
                $_SESSION['backPage']='pet.php?email='.$_SESSION['email'];
                echo "<td colspan='2'> <input type='submit' class='btn btn-success' value='".UPDATE_PET."'></td></tr></form>";

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
    </form>';
// Button to download the QR code image
                echo '<a href="'.$qrPicture.'" download class="btn btn-secondary mt-3">'.DOWNLOAD_QRCODE.'</a>';
                echo '</div>';

                echo '</div>';
            }
        }
        echo '</div>';

        echo '</div>';
    } }else {
        header('Location: index.php');
        exit();
    }
    ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
