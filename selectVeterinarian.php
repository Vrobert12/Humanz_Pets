<?php
session_start();
include "functions.php";

$functions = new Functions();

try {
    $connection = $functions->connect($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $pdoOptions);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Bejelentkezés ellenőrzése
$lang = $functions->language();
$functions->checkAutoLogin('selectVeterinarian.php');

$_SESSION['backPic'] = "selectVeterinarian.php";

// --- AJAX kérés feldolgozása ---
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    // Veterináriusok lekérdezése (szűrés email alapján)
    $sql = "SELECT * FROM veterinarian";
    $params = [];

    if (!empty($_GET['searchVetEmail'])) {
        $sql .= " WHERE veterinarianMail LIKE :email";
        $params[':email'] = '%' . $_GET['searchVetEmail'] . '%';
    }

    $stmt = $connection->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val, PDO::PARAM_STR);
    }
    $stmt->execute();
    $vets = $stmt->fetchAll();

    if (!$vets) {
        echo '<div class="alert alert-warning text-center">'.NO_VET_FOUND.'</div>';
    } else {
        foreach ($vets as $row) {
            echo '<form method="post" action="functions.php" class="mb-4 p-3 bg-light rounded shadow-sm" enctype="multipart/form-data" style="max-width: 800px; margin:auto;">';

            // Felső rész: profilkép és adatok egy sorban
            echo '<div class="row align-items-center text-center">';
            echo '<div class="col-md-6">';
            echo "<img src='pictures/{$row['profilePic']}' alt='Profile Picture' class='img-fluid rounded-circle' style='width: 250px; height: 250px;'>";
            echo '</div>';
            echo '<div class="col-md-6 text-start">';
            echo "<h5>{$row['firstName']} {$row['lastName']}</h5>";
            echo "<p><strong>".EMAIL.":</strong> {$row['veterinarianMail']}</p>";
            echo "<p><strong>".PHONE.":</strong> {$row['phoneNumber']}</p>";
            echo '</div>';
            echo '</div>';

            // Alsó rész: gomb külön sorban, középre igazítva
            echo '<div class="row mt-3">';
            echo '<div class="col text-center">';
            echo "<input type='hidden' name='action' value='veterinarianChose'>";
            echo "<input type='hidden' name='veterinarianId' value='{$row['veterinarianId']}'>";
            echo "<input type='submit' class='btn btn-primary' value='".CHOOSE_VET_BTN."'>";
            echo '</div>';
            echo '</div>';

            echo '</form>';
        }

    }
    exit();
}

// --- Normál oldal betöltése ---

// Kiválasztandó háziállatok lekérdezése (ahol még nincs állatorvos hozzárendelve)
$sql = "SELECT p.petId, p.petName, p.bred, p.petSpecies, u.userMail, p.profilePic
        FROM user u
        INNER JOIN pet p ON u.userId = p.userId
        WHERE u.userId = :userId AND veterinarId IS NULL";

$stmt = $connection->prepare($sql);
$stmt->bindParam(':userId', $_SESSION['userId'], PDO::PARAM_INT);
$stmt->execute();
$pets = $stmt->fetchAll();

if (count($pets) == 0) {
    // Ha nincs ilyen háziállat, irány vissza az indexre
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <script src="indexJS.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

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
<body style="background: #659df7">
<script src="searchVetInPetReg.js"></script>

<?php if (isset($_SESSION['title'])): ?>
    <h2 style="color: #2a7e2a"><?= htmlspecialchars($_SESSION['title']) ?></h2>
<?php endif; ?>

<?php $_SESSION['backPage'] = 'pet.php'; ?>

<div class="mainForm">

    <?php foreach ($pets as $pet): ?>
    <form action="updateAnimal.php" method="get" style="margin-bottom: 20px; max-width: 600px; margin-left: auto; margin-right: auto;">
        <input type="hidden" name="petName" value="<?= htmlspecialchars($pet['petName']) ?>">
        <input type="hidden" name="bred" value="<?= htmlspecialchars($pet['bred']) ?>">
        <input type="hidden" name="petSpecies" value="<?= htmlspecialchars($pet['petSpecies']) ?>">
        <input type="hidden" name="petPicture" value="<?= htmlspecialchars($pet['profilePic']) ?>">
        <h1 style="color: #2a7e2a; text-align:center;"><?php echo SELECT_YOUR_VET;?> for <?= htmlspecialchars($pet['petName']) ?></h1>
        <table class="profile-table" style="margin: auto;">
            <tr>
                <td rowspan="4" style="padding: 20px; text-align: center;">
                    <img alt="Profile Picture" width="200" height="200" src="pictures/<?= htmlspecialchars($pet['profilePic']) ?>">
                </td>
            </tr>
            <tr><td><?php echo NAME;?>: <?= htmlspecialchars($pet['petName']) ?></td></tr>
            <tr><td><?php echo BREED;?>: <?= htmlspecialchars($pet['bred']) ?></td></tr>
            <tr><td><?php echo SPECIES;?>: <?= htmlspecialchars($pet['petSpecies']) ?></td></tr>
            <?php if (isset($_SESSION['message'])): ?>
                <tr>
                    <td colspan="2" class="warning" style="text-align:center;">
                        <?php echo $_SESSION['message']; ?>
                    </td>
                </tr>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
        </table>
        <div style="display: flex; gap: 20px; max-width: 600px; margin: 15px auto 0 auto;">
            <form action="updateAnimal.php" method="get" style="flex: 1; margin: 0;">
                <input type="submit"style=" max-width: 270px;" class="btn btn-success w-100" value="<?php echo UPDATE_PET;?>">
            </form>

            <form action="registerAnimal.php" method="post" style="flex: 1; margin: 0;">
                <input type="hidden" name="action" value="deletePet">
                <input type="submit" style=" max-width: 350px;" class="btn btn-danger w-100" value="<?php echo DELETE_PET;?>">
            </form>
        </div>


        <?php
        $_SESSION['petId'] = $pet['petId'];
        $_SESSION['petPicture'] = $pet['profilePic'];
        endforeach;
        ?>


        <h1 style="color: #2a7e2a; text-align:center;"><?php echo LIST_OF_VETS;?></h1>

        <form id="searchForm" method="get" class="mb-4" onsubmit="return false;" style="max-width: 400px; margin: auto;">
            <input type="text" id="searchVetEmail" name="searchVetEmail"
           placeholder="<?php echo EMAIL;?>"
           class="form-control shadow-lg border-2 border-success"
           style="font-size: 20px; padding: 15px; border-radius: 15px;">
        </form>

        <div id="vetList" class="d-flex flex-column align-items-center" style="gap: 15px; max-width: 900px; margin: auto;">
            <!-- Az állatorvosok listája ide töltődik AJAX-szal -->
        </div>

</body>
</html>
