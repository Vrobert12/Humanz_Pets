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
<div class="container my-5">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="index.php" class="btn btn-secondary"><?php echo BACK?></a>
    </div>

    <?php
    include "functions.php";
    $autoload = new Functions();
    $autoload->checkAutoLogin();

    if (isset($_SESSION['email']) && isset($_GET['email'])) {
        $userID = $_SESSION['userId'];
        $functions = new Functions();
        // Use the connect method from the Functions class
        $connection = $functions->connect($GLOBALS['dsn'], PARAMS['USER'], PARAMS['PASSWORD'], $GLOBALS['pdoOptions']);

        // Fetch QR Code
        $sql = "SELECT qrCodeName FROM qr_code WHERE userId = :userId";
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(":userId", $userID, PDO::PARAM_INT);
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $qrPicture = htmlspecialchars($row['qrCodeName']);
                ?>
                <div class="row mb-5">
                    <div class="col-md-4">
                        <img src="<?= $qrPicture ?>" alt="QR Code" class="img-fluid rounded" style="max-width: 100%; height: auto;">
                    </div>
                    <div class="col-md-8">
                        <p class="lead"><?php echo INFO?></p>
                    </div>
                </div>
                <?php
            }
        }

        // Fetch User and Pet Information
        $sql = "SELECT p.petName, p.bred, p.petSpecies, u.userMail, p.petPicture 
                FROM user u 
                INNER JOIN pet p ON u.userId = p.userId 
                WHERE u.userId = :userId";

        $stmt = $connection->prepare($sql);
        $stmt->bindParam(":userId", $userID, PDO::PARAM_INT);
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $petName = htmlspecialchars($row['petName']);
                $typeOfAnimal = htmlspecialchars($row['bred']);
                $petSpecies = htmlspecialchars($row['petSpecies']);
                $userMail = htmlspecialchars($row['userMail']);
                $petPicture = htmlspecialchars($row['petPicture']);
                ?>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <img src="pictures/<?= $petPicture ?>" alt="Pet Picture" class="img-fluid rounded" style="max-width: 100%; height: auto;">
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th><?php echo NAME?></th>
                                <td><?= $petName ?></td>
                            </tr>
                            <tr>
                                <th><?php echo BREED?></th>
                                <td><?= $typeOfAnimal ?></td>
                            </tr>
                            <tr>
                                <th><?php echo SPECIES?></th>
                                <td><?= $petSpecies ?></td>
                            </tr>
                            <tr>
                                <th><?php echo EMAIL?></th>
                                <td><?= $userMail ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="alert alert-warning">'.NOPET.'</div>';
        }
    } else {
        header('Location: index.php');
        exit();
    }
    ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
