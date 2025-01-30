<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive Meta Tag -->
    <title>User Data</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Optional: Custom Styles -->
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: #659df7">
<div class="container my-5">
    <?php
    include "functions.php";
    $autoload=new Functions();
    $lang=$autoload->language();
    include "lang_$lang.php";
    $autoload->checkAutoLogin();

    if (isset($_SESSION['message'])) {
        echo "<div class='alert alert-info'>" . $_SESSION['message'] . "</div>";
        unset($_SESSION['message']);
    }

    $functions = new Functions();

    if (isset($_SESSION['email']) && isset($_GET['email'])) {
        $userID = $_SESSION['userId'];

        // Use the connect method from the Functions class
        $connection = $functions->connect($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $pdoOptions);

        if ($_SESSION['privilage'] == 'Veterinarian') {
            $sql = "SELECT firstName, lastName, phoneNumber, veterinarianMail, profilePic, registrationTime 
                FROM `veterinarian` WHERE veterinarianID = :veterinarianId";

            $stmt = $connection->prepare($sql);
            $stmt->bindParam(":veterinarianId", $userID, PDO::PARAM_INT);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $firstName = htmlspecialchars($row['firstName']);
                $lastName = htmlspecialchars($row['lastName']);
                $phoneNumber = htmlspecialchars($row['phoneNumber']);
                $userMail = htmlspecialchars($row['veterinarianMail']);
                $profilePic = htmlspecialchars($row['profilePic']);
                $privilage = htmlspecialchars('Veterinarian');
                $registrationTime = htmlspecialchars($row['registrationTime']);
            }
        } else {
            $sql = "SELECT firstName, lastName, phoneNumber, userMail, profilePic, privilage, registrationTime 
                FROM `user` WHERE userID = :userId";

            $stmt = $connection->prepare($sql);
            $stmt->bindParam(":userId", $userID, PDO::PARAM_INT);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $firstName = htmlspecialchars($row['firstName']);
                $lastName = htmlspecialchars($row['lastName']);
                $phoneNumber = htmlspecialchars($row['phoneNumber']);
                $userMail = htmlspecialchars($row['userMail']);
                $profilePic = htmlspecialchars($row['profilePic']);
                $privilage = htmlspecialchars($row['privilage']);
                $registrationTime = htmlspecialchars($row['registrationTime']);
            }
        }
            ?>
            <!-- User Profile Card -->
            <div class="card shadow mb-5">
                <div class="row g-0">
                    <div class="col-md-4 text-center p-3">
                        <img src="pictures/<?= $profilePic ?>" alt="Profile Picture" class="img-fluid rounded-circle"
                             style="max-width: 200px;">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title"><?= NAME ?>: <?= $firstName . ' ' . $lastName ?></h5>
                            <p class="card-text"><strong><?= PHONE ?>:</strong> <?= $phoneNumber ?></p>
                            <p class="card-text"><strong>Email:</strong> <?= $userMail ?></p>
                            <p class="card-text"><strong><?= PRIVILEGE ?>:</strong> <?= $privilage ?></p>
                            <p class="card-text"><strong><?= REGTIME ?>:</strong> <?= $registrationTime ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php

    } else {
        header('Location: index.php');
        exit();
    }
    ?>
    <!-- Back Button -->
    <div class="text-center">
        <a href="index.php" class="btn btn-secondary"><?php echo BACK ?></a>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
