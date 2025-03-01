<?php

require 'vendor/autoload.php';

include "functions.php";
$functions=new Functions();
$lang=$functions->language();
$functions->checkAutoLogin();


// Database connection
$pdo = $functions->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);


if (isset($_GET['user'])){
    $sqlMaxUser = $pdo->prepare("SELECT userId, userMail FROM user where userId=:userId");
    $sqlMaxUser->bindValue(':userId', $_GET['user']);
    $sqlMaxUser->execute();
    $userResult = $sqlMaxUser->fetch(PDO::FETCH_ASSOC);
    if($userResult==0){
        header('Location: book_veterinarian.php');
        exit();
    }
    $userId = $_GET['user'];
    $veterinarianId=$_SESSION['userId'];

    if ($userId!=$userResult['userId']) {
        header('Location: book_veterinarian.php');
        exit();
    }
}
if ($_SESSION['privilage'] == "Veterinarian")
    $userId = $_GET['user'] ?? null;
else
    $userId = $_SESSION['userId'] ?? null;

// Fetch user and pet details

if (!$userId) {
    header('Location: index.php');
    exit();
}
if($_SESSION['privilage']!="Veterinarian") {
    $petQuery = "SELECT p.petId, p.petName, p.bred, p.petSpecies, p.petPicture, p.veterinarId
FROM pet p
WHERE p.userId = :userId
AND p.petId NOT IN (
    SELECT r.petId
    FROM reservation r
    WHERE r.reservationDay >= CURDATE() AND r.animalChecked=0
)
";
    $petStmt = $pdo->prepare($petQuery);
    $petStmt->bindParam(":userId", $userId, PDO::PARAM_INT);
    $petStmt->execute();
    $pets = $petStmt->fetchAll(PDO::FETCH_ASSOC);

    $petQuery = "SELECT v.veterinarianId, v.veterinarianMail, p.petId, p.petName, p.bred, p.petSpecies, p.petPicture, r.reservationDay, r.reservationTime, r.period,r.reservationId
FROM pet p
LEFT JOIN reservation r ON p.petId = r.petId 
INNER JOIN veterinarian v ON r.veterinarianId = v.veterinarianId
WHERE p.userId = :userId
AND (r.reservationDay IS NOT NULL AND r.reservationDay >= CURDATE() AND r.animalChecked=0)

";

    $reservedPetStmt = $pdo->prepare($petQuery);
    $reservedPetStmt->bindParam(":userId", $userId, PDO::PARAM_INT);
    $reservedPetStmt->execute();
    $reservedPets = $reservedPetStmt->fetchAll(PDO::FETCH_ASSOC);
// Handle reservation submission
}
else{
    $petQuery = "SELECT p.petId, p.petName, p.bred, p.petSpecies, p.petPicture, p.veterinarId
FROM pet p
WHERE p.userId = :userId
AND p.petId NOT IN (
    SELECT r.petId
    FROM reservation r
    WHERE r.reservationDay >= CURDATE() AND r.animalChecked=0
) AND veterinarId = :veterinarianId
";
    $petStmt = $pdo->prepare($petQuery);
    $petStmt->bindParam(":userId", $userId, PDO::PARAM_INT);
    $petStmt->bindParam(":veterinarianId", $_SESSION['userId'], PDO::PARAM_INT);
    $petStmt->execute();
    $pets = $petStmt->fetchAll(PDO::FETCH_ASSOC);

    $petQuery = "SELECT v.veterinarianId, v.veterinarianMail, p.petId, p.petName, p.bred, p.petSpecies, p.petPicture, r.reservationDay, r.reservationTime, r.period,r.reservationId
FROM pet p
LEFT JOIN reservation r ON p.petId = r.petId 
INNER JOIN veterinarian v ON r.veterinarianId = v.veterinarianId
WHERE p.userId = :userId
AND (r.reservationDay IS NOT NULL AND r.reservationDay >= CURDATE() AND r.animalChecked=0) AND veterinarId = :veterinarianId

";

    $reservedPetStmt = $pdo->prepare($petQuery);
    $reservedPetStmt->bindParam(":userId", $userId, PDO::PARAM_INT);
    $reservedPetStmt->bindParam(":veterinarianId", $_SESSION['userId'], PDO::PARAM_INT);
    $reservedPetStmt->execute();
    $reservedPets = $reservedPetStmt->fetchAll(PDO::FETCH_ASSOC);
// Handle reservation submission

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    <script>
        const lang = '<?php echo $lang; ?>';
    </script>
    <script src="sureCheck.js"></script>

    <style>
        .popup-message {
            display: none;
            position: fixed;
            top: 100px; /* Initially off-screen */
            left: 50%;
            transform: translateX(-50%); /* Center horizontally */
            background-color: #212529;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 16px;
            z-index: 9999;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: auto;
            max-width: 80%;
            min-width: 300px;
            animation: slideInOut 10s ease-in-out;
        }
        .pet-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: border 0.3s ease, box-shadow 0.3s ease;
        }

        .pet-card img {
            width: 100%;
            max-width: 150px;
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

        .profile-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        @media (max-width: 576px) {
            .pet-card {
                padding: 0.5rem;
            }

            .pet-card img {
                max-width: 100px;
            }
        }
        @media (min-width: 768px) {
            .container {
                max-width: 900px;
            }

            .pet-card {
                max-width: 250px;
            }
        }
        @media (min-width: 1200px) {
            .container {
                max-width: 800px;
            }


        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const today = new Date().toISOString().split('T')[0];
            const reservationDate = document.querySelector('[name="day"]');
            const reservationTimeStart = document.querySelector('[name="reservationTimeStart"]');
            const reservationTimeEnd = document.querySelector('[name="reservationTimeEnd"]');
            const petRadios = document.querySelectorAll('[name="petId"]');

            let selectedVeterinarianId = null;

            function getAllowedHours(dayOfWeek) {
                return (dayOfWeek >= 1 && dayOfWeek <= 5) ? { start: 8, end: 20 } : { start: 8, end: 12 };
            }

            function populateTimeOptions(startHour, endHour) {
                reservationTimeStart.innerHTML = "";
                reservationTimeEnd.value = ""; // Clear end time when options are updated
                const times = [<?php echo json_encode(SELECT_TIME); ?>];

                for (let hour = startHour; hour <= endHour; hour++) {
                    const timeString = (hour < 10 ? '0' : '') + hour + ":00";
                    times.push(timeString);
                }

                times.forEach((time, index) => {
                    const option = document.createElement("option");
                    option.value = time;
                    option.textContent = time;

                    if (index === 0) {
                        option.selected = true;
                        option.disabled = true;
                        option.hidden = true;
                    }

                    reservationTimeStart.appendChild(option);
                });
            }

            async function fetchAvailableTimes() {
                const selectedDate = reservationDate.value;
                reservationTimeEnd.value = ""; // Clear end time when date changes

                if (!selectedVeterinarianId || !selectedDate) return;

                if (selectedDate <= today) {
                    alert("You cannot select a past date.");
                    reservationDate.value = '';
                    reservationTimeStart.disabled = true;
                    return;
                }

                const dateObject = new Date(selectedDate);
                const dayOfWeek = dateObject.getDay();
                const { start, end } = getAllowedHours(dayOfWeek);

                populateTimeOptions(start, end);

                try {
                    const response = await fetch(`check_availability.php?date=${selectedDate}&veterinarianId=${selectedVeterinarianId}`);
                    const data = await response.json();

                    if (data.isFullyBooked) {
                        alert("This date is fully booked. Please select another date.");
                        reservationDate.value = '';
                        reservationTimeStart.disabled = true;
                        return;
                    }

                    reservationTimeStart.disabled = false;

                    if (data.reservedTimes) {
                        Array.from(reservationTimeStart.options).forEach(option => {
                            if (data.reservedTimes.includes(option.value)) {
                                option.disabled = true;
                                option.hidden = true;
                            } else {
                                option.disabled = false;
                                option.hidden = option.textContent === <?php echo json_encode(SELECT_TIME); ?>;
                            }
                        });
                    }
                } catch (error) {
                    console.error("Error fetching availability:", error);
                }
            }

            petRadios.forEach(radio => {
                radio.addEventListener("change", async function () {
                    if (this.checked) {
                        const petId = this.value;
                        reservationTimeEnd.value = ""; // Clear end time when pet changes

                        // Fetch veterinarian ID for the selected pet
                        try {
                            const response = await fetch(`get_veterinarian.php?petId=${petId}`);
                            const data = await response.json();

                            if (data.veterinarianId) {
                                selectedVeterinarianId = data.veterinarianId;
                                console.log("Selected Veterinarian:", selectedVeterinarianId);

                                reservationDate.disabled = false;

                                if (reservationDate.value) {
                                    fetchAvailableTimes();
                                }
                            } else {
                                alert("No veterinarian assigned to this pet.");
                            }
                        } catch (error) {
                            console.error("Error fetching veterinarian:", error);
                        }
                    }
                });
            });

            reservationDate.addEventListener("change", fetchAvailableTimes);

            reservationTimeStart.addEventListener("change", function () {
                const startTime = reservationTimeStart.value;
                reservationTimeEnd.value = ""; // Clear end time when start time changes

                if (!startTime) return;

                let endHour = parseInt(startTime.split(":")[0]) + 1;
                const selectedDate = reservationDate.value;
                const dayOfWeek = new Date(selectedDate).getDay();
                const { end } = getAllowedHours(dayOfWeek);

                if (endHour > end) endHour = end;

                const endTime = (endHour < 10 ? '0' : '') + endHour + ":00";
                reservationTimeEnd.value = endTime;
            });
        });

    </script>
</head>

<body class="container py-4" style="background: #659df7">

<!-- Show popup message if session message is set -->
<?php if (isset($_SESSION['message'])): ?>
<div class="popup-message" id="popupMessage">
    <?php echo $_SESSION['message']; ?>
</div>
<?php unset($_SESSION['message']); // Clear message after it's displayed ?>
<?php endif; ?>

<script>

    // Show the popup message and hide it after 5 seconds
    window.onload = function () {
        var popupMessage = document.getElementById('popupMessage');
        if (popupMessage) {
            popupMessage.style.display = 'block';  // Show the popup

            // Hide the popup after 5 seconds
            setTimeout(function () {
                popupMessage.style.display = 'none';
            }, 5000);
        }
    };
</script>
<!--
https://getbootstrap.com/docs/5.3/components/navbar/
-->
<?php
if($_SESSION['privilage']=="Veterinarian")
    echo '<a href="book_veterinarian.php" class="btn btn-success mt-4">BACK</a>';

else
echo '<a href="index.php" class="btn btn-success mt-4"> BACK</a>';
if (isset($_SESSION['message']) && $_SESSION['message'] != "")
    echo "<div class='mainBlock rounded bg-dark text-white' style='text-align: center; margin-top: 100px;'>
          <h1 style='margin: auto;'>
              " . $_SESSION['message'] . "
          </h1>
          <a class='inputok' onclick='refreshPage()' style='display: inline-block; padding: 10px 20px; 
             background-color: #19451e; color: white; text-decoration: none; border-radius: 5px; 
             cursor: pointer; transition: background-color 0.3s ease; margin-top: 20px;'>
              Okay
          </a>
      </div>";
if($_SESSION['privilage']=="Veterinarian")
    echo '<h2 class="text-center mb-4">'.RESERVED_APOINTMENT_TITLE_VET.' '. $userResult['userMail'] .'</h2>';

?>

<?php if (isset($_SESSION['reservationMessage'])): ?>
    <div class="alert alert-info"> <?= htmlspecialchars($_SESSION['reservationMessage']) ?> </div>
    <?php unset($_SESSION['reservationMessage']); endif; ?>

<form method="POST" action="functions.php">
    <div class="mb-3">
        <?php if (!empty($pets))
            echo' <h4 for="pet" class="form-label">'.SELECT_PET.'</h4>';
        ?>

        <div class="profile-section">
            <?php foreach ($pets as $pet): ?>
                <div class="pet-card">
                    <input type="radio" id="pet-<?= htmlspecialchars($pet['petId']) ?>" name="petId" value="<?= htmlspecialchars($pet['petId']) ?>" required>
                    <label for="pet-<?= htmlspecialchars($pet['petId']) ?>">
                        <?php
                        $sql="SELECT veterinarianMail,veterinarianId FROM veterinarian v inner join pet p ON v.veterinarianId=p.veterinarId WHERE petId=:petId";
                        $sql=$pdo->prepare($sql);
                        $sql->bindValue(':petId',$pet['petId']);
                        $sql->execute();
                        $result=$sql->fetch();
if($_SESSION['privilage']!="Veterinarian")
                        echo '<h5 class="text-center mb-4">' . PETS_VETERINARIAN . ' ' . $result['veterinarianMail'] . '</h5>';?>
                        <span class="custom-radio"></span>
                        <img alt="Pet Picture" src="pictures/<?= htmlspecialchars($pet['petPicture']) ?>">
                        <p class="pet-details"> <?= htmlspecialchars($pet['petName']) ?> </p>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="mb-3">
        <label for="day" class="form-label"><?php echo RESERVATION3;?></label>
        <input type="date" class="form-control" name="day" required>
    </div>

    <div class="mb-3">
        <label for="reservationTimeStart" class="form-label"><?php echo RESERVATION;?></label>
        <select class="form-select" name="reservationTimeStart" required disabled></select>
    </div>

    <div class="mb-3">
        <label for="reservationTimeEnd" class="form-label"><?php echo RESERVATION2;?></label>
        <input type="text" class="form-control" name="reservationTimeEnd" readonly>
    </div>

    <input type="hidden" value="insertReservation" name="action">
    <?php if($_SESSION['privilage']=="Veterinarian")
        echo ' <input type="hidden" name="veterinarianId" value="'.$_SESSION['userId'].'">';
    ?>


    <button type="submit" class="btn btn-primary w-100"><?php echo RESERVE;?></button>
</form>

<h3 class="mt-5"><?php echo RESERVED_PETS_TITLE; ?></h3>
<div class="profile-section">
    <?php foreach ($reservedPets as $reservedPet): ?>
        <div class="pet-card">
            <label>
                <img alt="Pet Picture" src="pictures/<?= htmlspecialchars($reservedPet['petPicture']) ?>">

                <?php
                if($_SESSION['privilage']!="Veterinarian")
                    echo '   <p class="pet-details"> <?php echo RESERVED_VETERINARIAN; ?> </p><p class="pet-details">'.$reservedPet['veterinarianMail'] .'</p>';
                ?>

                <p class="pet-details"> <?= htmlspecialchars($reservedPet['petName']) ?> </p>
                <p> <?= htmlspecialchars($reservedPet['reservationDay']) ?> </p>
                <p> <?= htmlspecialchars($reservedPet['reservationTime']) . "-" . htmlspecialchars($reservedPet['period']) ?> </p>
                <?php if($_SESSION['privilage']!="Veterinarian")
                    echo ' <form method="post" action="functions.php">
                    <input type="hidden" value="',$reservedPet['reservationId'].'" name="reservationId">
                    <input type="hidden" value="deleteReservation" name="action">
                  
                         <input type="hidden" value="'.$reservedPet['veterinarianId'] .'" name="veterinarian">
                    <input type="hidden" value="'. $_SESSION['userId'] .'" name="veterinarian">
                    
                    <input type="submit" value="'.DELETE_RESERVATION_BUTTON.'" class="btn btn-danger btn-sm" onclick="confirmDeletingApointment(event)">
                </form>'; ?>
            </label>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
