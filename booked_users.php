<?php

include "functions.php";
$autoload = new Functions();
$lang = $autoload->language();
include "lang_$lang.php";
$autoload->checkAutoLogin();

if($_SESSION['privilage'] != 'Veterinarian'){
    header('location: index.php');
    exit();
}

$veterinarianId=$_SESSION['userId'];
$pdo = $autoload->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);

$petQuery = "SELECT u.userMail,u.userId,u.usedLanguage,p.petId, p.petName, p.bred, p.petSpecies, p.petPicture, r.reservationDay, r.reservationTime, r.period,r.reservationId
FROM pet p
LEFT JOIN reservation r ON p.petId = r.petId INNER JOIN user u ON p.userId = u.userId
WHERE r.veterinarianId=:veterinarianId AND r.animalChecked=0
AND (r.reservationDay IS NOT NULL AND r.reservationDay >= CURDATE()) ORDER BY r.reservationDay ASC, r.reservationTime ASC";

$reservedPetStmt = $pdo->prepare($petQuery);
$reservedPetStmt->bindParam(":veterinarianId", $veterinarianId, PDO::PARAM_INT);
$reservedPetStmt->execute();
$reservedPets = $reservedPetStmt->fetchAll(PDO::FETCH_ASSOC);
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
            width: 250px;
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
</head>
<body>


<div class="profile-section"><a class="btn btn-secondary mb-4" href="index.php"><?php echo BACK ?></a>
    <h2>Reserved Appointment for you</h2> </div>
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
<div class="profile-section">
    <?php
    if (count($reservedPets) > 0) {
        foreach ($reservedPets as $reservedPet) {
            if($reservedPet['usedLanguage'] == 'hu')
                $language = LANGUAGE_hu;
            if($reservedPet['usedLanguage'] == 'sr')
                $language = LANGUAGE_sr;
            if($reservedPet['usedLanguage'] == 'en')
                $language = LANGUAGE_en;
            $_SESSION['backPic']='booked_users.php';
            ?>

            <div class="pet-card">
                <a class="btn btn-success btn-sm"
                   href="petsInfo.php?petId=<?= htmlspecialchars($reservedPet['petId']) ?>">More Details</a><br>

                <label for="pet-<?= htmlspecialchars($reservedPet['petId']) ?>">
                    <img alt="Pet Picture" src="pictures/<?= htmlspecialchars($reservedPet['petPicture']) ?>">
                    <p class="pet-details"><?= htmlspecialchars($reservedPet['userMail']) ?></p>
                    <p class="pet-details"><?= htmlspecialchars($reservedPet['petName']) ?></p>
                    <p class="pet-details"><?= htmlspecialchars($language) ?></p>
                    <p><?= htmlspecialchars($reservedPet['reservationDay']) ?></p>
                    <p><?= htmlspecialchars($reservedPet['reservationTime']) . "-" . htmlspecialchars($reservedPet['period']) ?></p>
                    <form method="post" action="functions.php">
                        <label for="mailText">Reason for deleteing reservation:</label>
                        <input type="hidden" value="<?= $reservedPet['reservationId'] ?>" name="reservationId">
                        <input type="hidden" value="deleteReservationByVet" name="action">
                        <input type="hidden" name="cancelEmail" value="<?= htmlspecialchars($reservedPet['userMail']) ?>">
                        <input type="hidden" value="<?= htmlspecialchars($reservedPet['usedLanguage']) ?>" name="ownerLanguage">
                        <textarea  name="mailText"></textarea>
                        <input type="submit" value="Delete" class="btn btn-danger btn-sm" onclick="confirmDeletingApointment(event)">
                    </form>
                    <form method="post" action="functions.php">
                        <input type="hidden" value="<?= htmlspecialchars($reservedPet['reservationId']) ?>" name="reservationId">
                        <input type="hidden" value="<?= htmlspecialchars($reservedPet['userMail']) ?>" name="ownerMail">
                        <input type="hidden" value="<?= htmlspecialchars($reservedPet['userId']) ?>" name="ownerId">
                        <input type="hidden" value="deleteReservation" name="action">
                        <input type="submit" value="Check" class="btn btn-primary btn-sm" onclick="confirmCheck(event)" />
                    </form>

                </label>
            </div>

        <?php }
    } else {
        echo "No pets";
    }
    ?>

</div>
</body>
</html>
