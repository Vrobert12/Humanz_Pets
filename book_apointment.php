<?php

require 'vendor/autoload.php';

include "functions.php";
$functions=new Functions();
$lang=$functions->language();
include "lang_$lang.php";
$functions->checkAutoLogin();

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

$petQuery = "SELECT p.petId, p.petName, p.bred, p.petSpecies, p.petPicture
FROM pet p
WHERE p.userId = :userId
AND p.petId NOT IN (
    SELECT r.petId
    FROM reservation r
    WHERE r.reservationDay >= CURDATE()
)
";
$petStmt = $pdo->prepare($petQuery);
$petStmt->bindParam(":userId", $userId, PDO::PARAM_INT);
$petStmt->execute();
$pets = $petStmt->fetchAll(PDO::FETCH_ASSOC);

$petQuery = "SELECT p.petId, p.petName, p.bred, p.petSpecies, p.petPicture, r.reservationDay, r.reservationTime, r.period,r.reservationId
FROM pet p
LEFT JOIN reservation r ON p.petId = r.petId
WHERE p.userId = :userId
AND (r.reservationDay IS NOT NULL AND r.reservationDay >= CURDATE())

";

$reservedPetStmt = $pdo->prepare($petQuery);
$reservedPetStmt->bindParam(":userId", $userId, PDO::PARAM_INT);
$reservedPetStmt->execute();
$reservedPets = $reservedPetStmt->fetchAll(PDO::FETCH_ASSOC);
// Handle reservation submission



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    <style>

        .profile-section {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .pet-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 200px;
            text-align: center;
            cursor: pointer;
            transition: border 0.3s ease, box-shadow 0.3s ease;
        }

        .pet-card img {
            width: 150px;
            height: 150px;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }

        .pet-card input[type="radio"] {
            display: none;
        }

        .pet-card label {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            gap: 0.5rem;
        }

        .custom-radio {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #ddd;
            border-radius: 50%;
            position: relative;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }

        .custom-radio::after {
            content: '';
            display: block;
            width: 12px;
            height: 12px;
            background: #007bff;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        input[type="radio"]:checked + label .custom-radio {
            border-color: #007bff;
        }

        input[type="radio"]:checked + label .custom-radio::after {
            opacity: 1;
        }

        .pet-details {
            font-size: 1rem;
            font-weight: bold;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const today = new Date().toISOString().split('T')[0];
            const reservationDate = document.querySelector('[name="day"]');
            const reservationTimeStart = document.querySelector('[name="reservationTimeStart"]');
            const veterinarianId = <?= htmlspecialchars($veterinarianId) ?>;

            // Set the allowed hours (9:00 to 20:00)
            const allowedStartTime = "09:00";
            const allowedEndTime = "20:00";

            // Disable time inputs initially
            reservationTimeStart.disabled = true;

            // Populate the select options for time slots
            function populateTimeOptions() {
                const times = [];
                let currentTime = 9; // Starting from 9:00 AM
                times.push("Select time");

                // Create options for each hour between 9 and 20 (9:00 to 20:00)
                while (currentTime <= 20) {
                    const timeString = (currentTime < 10 ? '0' : '') + currentTime + ":00";
                    times.push(timeString);
                    currentTime++;
                }

                // Populate the start time select with options
                times.forEach((time, index) => {
                    const startOption = document.createElement("option");
                    startOption.value = time;
                    startOption.textContent = time;

                    // Make the first option the "Select time" option, visible but not selectable
                    if (index === 0) {
                        startOption.textContent = "Select time";
                        startOption.selected = true;
                        startOption.disabled = true;
                        startOption.hidden = true; // This makes it unselectable
                    }

                    reservationTimeStart.appendChild(startOption);
                });
            }

            populateTimeOptions();

            // Enable and validate inputs based on the selected date
            reservationDate.addEventListener("change", async function () {
                const selectedDate = reservationDate.value;

                if (selectedDate <= today) {
                    alert("You cannot select a past date.");
                    reservationDate.value = '';
                    reservationTimeStart.disabled = true;
                    return;
                }

                // Reset time dropdown to default state
                reservationTimeStart.value = "Select time";  // Reset the time selection to "Select time"
                reservationTimeStart.disabled = true;  // Disable the time dropdown while fetching availability

                // Fetch available time slots for the selected date
                const response = await fetch(`check_availability.php?date=${selectedDate}&veterinarianId=${veterinarianId}`);
                const data = await response.json();

                // Disable unavailable time slots
                if (data.isFullyBooked) {
                    alert("This date is fully booked. Please select another date.");
                    reservationDate.value = '';
                    reservationTimeStart.disabled = true;
                } else {
                    // Enable time slots and disable the ones that are already taken
                    reservationTimeStart.disabled = false;

                    // Disable the reserved time slots
                    const reservedTimes = data.reservedTimes; // Array of reserved times on the selected date

                    // Debugging: Log reserved times
                    console.log("Reserved times:", reservedTimes);

                    // Iterate through the options and disable the ones that are reserved
                    Array.from(reservationTimeStart.options).forEach(option => {
                        // Log each option value and reserved time comparison
                        console.log(`Checking option value: ${option.value}`);
                        if (reservedTimes.includes(option.value)) {
                            option.disabled = true; // Disable the option if it's in reservedTimes
                            option.hidden=true;
                            console.log(`Disabled time slot: ${option.value}`);
                        } else {
                            option.disabled = false; // Enable the option if it's not reserved
                            if (option.textContent !== "Select time")
                                option.hidden=false;
                        }
                    });
                }
            });

            // Enable and automatically calculate the end time based on start time
            reservationTimeStart.addEventListener("change", function () {
                const startTime = reservationTimeStart.value;

                if (startTime && startTime >= allowedStartTime && startTime <= allowedEndTime) {
                    // Calculate the end time by adding 1 hour to the selected start time
                    let endHour = parseInt(startTime.split(":")[0]) + 1;
                    if (endHour > 20) endHour = 20; // Ensure end time doesn't exceed 20:00

                    const endTime = (endHour < 10 ? '0' : '') + endHour + ":00"; // Format end time (e.g., 10:00)

                    // Set the end time value to 1 hour later
                    document.querySelector('[name="reservationTimeEnd"]').value = endTime;
                }
            });
        });

    </script>
</head>
<body>
<h2>Reserve Appointment for Veterinarian ID: <?= htmlspecialchars($veterinarianId) ?></h2>

<?php if (isset($_SESSION['reservationMessage'])): ?>
    <p><?= htmlspecialchars($_SESSION['reservationMessage']) ?></p>
    <?php unset($_SESSION['reservationMessage']); endif; ?>

<form method="POST" action="functions.php">
    <label for="pet">Select Pet:</label>
    <div class="profile-section">
        <?php foreach ($pets as $pet): ?>
            <div class="pet-card">

                <input type="radio" id="pet-<?= htmlspecialchars($pet['petId']) ?>" name="petId" value="<?= htmlspecialchars($pet['petId']) ?>" required>
                <label for="pet-<?= htmlspecialchars($pet['petId']) ?>">
                    <span class="custom-radio"></span>
                    <img alt="Pet Picture" src="pictures/<?= htmlspecialchars($pet['petPicture']) ?>">
                    <p class="pet-details"><?= htmlspecialchars($pet['petName']) ?></p>
                </label>
            </div>
        <?php endforeach; ?>
    </div>


    <label for="day">Reservation Date:</label>
    <input type="date" name="day" required>

    <label for="reservationTimeStart">Start Time:</label>
    <select name="reservationTimeStart" required disabled>
        <!-- Options will be populated here by JavaScript -->
    </select>

    <label for="reservationTimeEnd">End Time:</label>
    <input type="hidden" value="insertReservation" name="action">
    <input type="text" name="reservationTimeEnd" readonly> <!-- This will show the calculated end time -->
<input type="hidden" name="veterinarianId" value="<?= htmlspecialchars($_GET['veterinarian']) ?>">
    <input type="hidden" value="<?= $_GET['veterinarian']?>" name="veterinarian">
    <button type="submit">Reserve</button>
</form>
<label for="pet">Reserved Pet:</label>
<div class="profile-section">
    <?php foreach ($reservedPets as $reservedPet): ?>
        <div class="pet-card">

            <label for="pet-<?= htmlspecialchars($reservedPet['petId']) ?>">
                <img alt="Pet Picture" src="pictures/<?= htmlspecialchars($reservedPet['petPicture']) ?>">
                <p class="pet-details"><?= htmlspecialchars($reservedPet['petName']) ?></p>
                <p><?= htmlspecialchars($reservedPet['reservationDay']) ?></p>
                <p><?= htmlspecialchars($reservedPet['reservationTime'])."-".htmlspecialchars($reservedPet['period']) ?></p>
                <form method="post" action="functions.php">
                    <input type="hidden" value="<?= $reservedPet['reservationId']?>" name="reservationId">
                    <input type="hidden" value="deleteReservation" name="action">
                    <input type="hidden" value="<?= $_GET['veterinarian']?>" name="veterinarian">
                    <input type="submit" value="delete">

                </form>

            </label>
        </div>
    <?php endforeach; ?>
</div>


<a href="book_veterinarian.php">Back to Veterinarian Selection</a>
</body>
</html>
