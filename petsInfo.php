<?php

require 'vendor/autoload.php';

include "functions.php";
$functions = new Functions();
$lang = $functions->language();
$functions->checkAutoLogin();
if ($_SESSION['privilage'] != "Veterinarian") {
    header("Location: index.php");
    exit();
}

$vetID = $_SESSION['userId'];

$connection = $functions->connect($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $pdoOptions);

// --- AJAX kérés esetén csak a találatokat küldjük vissza ---
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    $searchPet = isset($_GET['searchPet']) ? trim($_GET['searchPet']) : '';
    $searchEmail = isset($_GET['searchEmail']) ? trim($_GET['searchEmail']) : '';

    $query = "SELECT u.firstName, u.lastName, u.userMail, u.phoneNumber, p.petId, p.petName, p.bred, p.petSpecies, p.profilePic
              FROM veterinarian v 
              INNER JOIN pet p ON v.veterinarianId = p.veterinarId 
              INNER JOIN reservation r ON r.petId = p.petId 
              INNER JOIN user u ON p.userId = u.userId 
              WHERE r.veterinarianId = :vetID ";

    if ($searchPet !== '') {
        $query .= " AND p.petName LIKE :searchPet";
    }

    if ($searchEmail !== '') {
        $query .= " AND u.userMail LIKE :searchEmail";
    }

    $stmt = $connection->prepare($query);
    $stmt->bindParam(":vetID", $vetID, PDO::PARAM_INT);

    if ($searchPet !== '') {
        $likePet = '%' . $searchPet . '%';
        $stmt->bindParam(":searchPet", $likePet, PDO::PARAM_STR);
    }
    if ($searchEmail !== '') {
        $likeEmail = '%' . $searchEmail . '%';
        $stmt->bindParam(":searchEmail", $likeEmail, PDO::PARAM_STR);
    }

    if ($stmt->execute()) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($results) > 0) {
            foreach ($results as $row) {
                echo '<div class="row mb-4 p-3 bg-light rounded shadow-sm">';
                echo '<div class="col-md-4 text-center">';
                echo '<img class="profile-image" alt="Pet Picture" src="pictures/' . htmlspecialchars($row['profilePic']) . '">';
                echo '</div>';
                echo '<div class="col-md-8">';
                echo '<h5>' . htmlspecialchars($row['petName']) . '</h5>';
                echo '<p><strong>' . BREED . ':</strong> ' . htmlspecialchars($row['bred']) . '</p>';
                echo '<p><strong>' . SPECIES . ':</strong> ' . htmlspecialchars($row['petSpecies']) . '</p>';
                echo '<p><strong>' . EMAIL . ':</strong> ' . htmlspecialchars($row['userMail']) . '</p>';
                echo '<p><strong>' . NAME . ':</strong> ' . htmlspecialchars($row['firstName']) . ' ' . htmlspecialchars($row['lastName']) . '</p>';
                echo '<p><strong>' . PHONE . ':</strong> ' . htmlspecialchars($row['phoneNumber']) . '</p>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<div class="alert alert-warning text-center fs-5">';
            echo defined('NOPET') ? NOPET : 'There are no upcoming reservations.';
            echo '</div>';
        }
    } else {
        echo '<div class="alert alert-danger text-center">Database query failed.</div>';
    }

    exit(); // kilépünk, nem küldünk több HTML-t
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
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

    <form id="searchForm" class="mb-4">
        <div class="row g-2">
            <div class="col-md-6">
                <input type="text" id="searchPet" name="searchPet" class="form-control" placeholder="<?php echo NAME_PET;?>" autocomplete="off" />
            </div>
            <div class="col-md-6">
                <input type="text" id="searchEmail" name="searchEmail" class="form-control" placeholder="<?php echo EMAIL;?>" autocomplete="off" />
            </div>
        </div>
    </form>

    <div id="results"></div>
</div>


<script src="searchPetVet.js">
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
