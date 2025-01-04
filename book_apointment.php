<?php

require 'vendor/autoload.php';

include "functions.php";
$functions = new Functions();
$functions->language();
$functions->checkAutoLogin();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    <style>
        .profile-section {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .profile-image {
            max-width: 100%;
            border-radius: 8px;
        }
        .pet-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1rem;
            display: flex;
            gap: 1rem;
        }
    </style>
    <script>
        function activateSubmit() {
            // Activate the submit button when a file is selected
            document.getElementById('submitButton').click();
        }

        function activateSubmit2() {
            // Activate the submit button when a file is selected
            document.getElementById('submit2').click();
        }

        function logoutAndRedirect() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'functions.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    // Redirect to MainPage.php after successful logout
                    window.location.href = 'reservation.php';
                } else {
                    // Handle logout error
                    console.error('Logout failed with status ' + xhr.status);
                }
            };
            xhr.send();
        }

        function fetchDishesByType() {
            var selectedType = document.getElementById("dishTypeSelect").value;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_dishes.php?dishType=" + selectedType, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById("dishContainer").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        function refreshPage() {
            window.location.reload();
        }
    </script>
    <!--    <script src="dishes.js"></script>-->
</head>
<body>
<?php
// Database connection
$pdo = $functions->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);

// Fetch the maximum veterinarian ID
$sqlMaxVet = $pdo->prepare("SELECT MAX(veterinarianId) AS maxVeterinarianId FROM veterinarian");
$sqlMaxVet->execute();
$maxVetResult = $sqlMaxVet->fetch(PDO::FETCH_ASSOC);
$_SESSION['maxVeterinarianId'] = $maxVetResult['maxVeterinarianId'] ?? null;

// Redirect if veterinarian parameter is invalid
$veterinarianId = $_GET['veterinarian'] ?? null;
if (!isset($veterinarianId) || $veterinarianId <= 0 || $veterinarianId > $_SESSION['maxVeterinarianId']) {
    header('Location: book_veterinarian.php');
    exit();
}

// Fetch user and pet details
$userId = $_SESSION['userId'] ?? null;
if (!$userId) {
    header('Location: index.php');
    exit();
}

$connection = $functions->connect($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $pdoOptions);
$petQuery = "SELECT p.petId, p.petName, p.bred, p.petSpecies, p.petPicture 
             FROM pet p WHERE p.userId = :userId";
$petStmt = $connection->prepare($petQuery);
$petStmt->bindParam(":userId", $userId, PDO::PARAM_INT);
$petStmt->execute();
$pets = $petStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle reservation submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedPetId = $_POST['petId'] ?? null;
    $reservationDate = $_POST['day'] ?? null;
    $reservationStart = $_POST['reservationTimeStart'] ?? null;
    $reservationEnd = $_POST['reservationTimeEnd'] ?? null;

    if ($selectedPetId && $reservationDate && $reservationStart && $reservationEnd) {
        // Check if the pet already has 5 reservations for the day
        $today = date("Y-m-d");
        $reservationCheckQuery = $pdo->prepare(
            "SELECT COUNT(*) AS reservationCount FROM reservation 
             WHERE petId = :petId AND reservationDay >= :today"
        );
        $reservationCheckQuery->execute([
            ':petId' => $selectedPetId,
            ':today' => $today
        ]);
        $reservationCount = $reservationCheckQuery->fetch(PDO::FETCH_ASSOC)['reservationCount'] ?? 0;

        if ($reservationCount < 5) {
            // Insert the reservation
            $insertQuery = $pdo->prepare(
                "INSERT INTO reservation (petId, veterinarianId, reservationDay, reservationTime, period) 
                 VALUES (:petId, :veterinarianId, :reservationDay, :reservationStart, :reservationEnd)"
            );
            $insertQuery->execute([
                ':petId' => $selectedPetId,
                ':veterinarianId' => $veterinarianId,
                ':reservationDay' => $reservationDate,
                ':reservationStart' => $reservationStart,
                ':reservationEnd' => $reservationEnd
            ]);

            $_SESSION['reservationMessage'] = "Reservation successfully created!";
        } else {
            $_SESSION['reservationMessage'] = "You already have too many reservations for this pet.";
        }
    } else {
        $_SESSION['reservationMessage'] = "All fields are required.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    <style>
        .profile-section {
            display: flex;
            gap: 1rem;
            flex-direction: row;
        }

        .pet-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1rem;
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            width: 100%;
        }

        .pet-card img {
            max-width: 100%;
            border-radius: 8px;
            cursor: pointer;
        }

        .pet-card label {
            cursor: pointer;
        }

        .pet-card input[type="radio"] {
            display: none;
        }

        .pet-card input[type="radio"]:checked + label img {
            border: 2px solid #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .pet-details {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-left: 1rem;
        }

        .selected-pet {
            border: 2px solid #007bff;
            padding: 10px;
            margin-top: 1rem;
        }

        .profile-image {
            max-width: 100px;
        }
    </style>
</head>
<body>
<h2>Reserve Appointment for Veterinarian ID: <?= htmlspecialchars($veterinarianId) ?></h2>

<?php if (isset($_SESSION['reservationMessage'])): ?>
    <p><?= htmlspecialchars($_SESSION['reservationMessage']) ?></p>
    <?php unset($_SESSION['reservationMessage']); endif; ?>

<form method="POST">
    <label for="pet">Select Pet:</label>
    <div class="profile-section">
        <?php foreach ($pets as $pet): ?>
            <div class="pet-card">
                <div class="col-md-4 text-center">
                    <!-- Radio button as the picture -->
                    <input type="radio" id="pet-<?= htmlspecialchars($pet['petId']) ?>" name="petId" value="<?= htmlspecialchars($pet['petId']) ?>" required>
                    <label for="pet-<?= htmlspecialchars($pet['petId']) ?>">
                        <img class="profile-image" alt="Pet Picture" src="pictures/<?= htmlspecialchars($pet['petPicture']) ?>">
                    </label>
                </div>
                <div class="col-md-8 pet-details">
                    <p><strong>Name:</strong> <?= htmlspecialchars($pet['petName']) ?></p>
                    <p><strong>Breed:</strong> <?= htmlspecialchars($pet['bred']) ?></p>
                    <p><strong>Species:</strong> <?= htmlspecialchars($pet['petSpecies']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <label for="day">Reservation Date:</label>
    <input type="date" name="day" required>

    <label for="reservationTimeStart">Start Time:</label>
    <input type="time" name="reservationTimeStart" required>

    <label for="reservationTimeEnd">End Time:</label>
    <input type="time" name="reservationTimeEnd" required>

    <button type="submit">Reserve</button>
</form>

<a href="book_veterinarian.php">Back to Veterinarian Selection</a>
</body>
</html>

