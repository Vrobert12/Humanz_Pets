<?php

require 'vendor/autoload.php';

include "functions.php";
$functions=new Functions();
$lang=$functions->language();
$functions->checkAutoLogin();


// Database connection
$pdo = $functions->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);

// Fetch the maximum veterinarian ID
if(isset($_GET['veterinarian'])) {
    $sqlMaxVet = $pdo->prepare("SELECT veterinarianId FROM veterinarian where veterinarianId = :veterinarianId");
    $sqlMaxVet->bindValue(':veterinarianId', $_GET['veterinarian']);
    $sqlMaxVet->execute();
    $vetResult = $sqlMaxVet->fetch(PDO::FETCH_ASSOC);
if($vetResult==0){
    header('Location: book_veterinarian.php');
    exit();
}
// Redirect if veterinarian parameter is invalid
    $veterinarianId = $_GET['veterinarian'];

    if ($vetResult['veterinarianId']!=$veterinarianId) {
        header('Location: book_veterinarian.php');
        exit();
    }
}
elseif (isset($_GET['user'])){
    $sqlMaxUser = $pdo->prepare("SELECT userId FROM user where userId=:userId");
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

$petQuery = "SELECT p.petId, p.petName, p.bred, p.petSpecies, p.petPicture
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

$petQuery = "SELECT v.veterinarianMail, p.petId, p.petName, p.bred, p.petSpecies, p.petPicture, r.reservationDay, r.reservationTime, r.period,r.reservationId
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
            const veterinarianId = <?= htmlspecialchars($veterinarianId) ?>;

            const allowedStartTime = "09:00";
            const allowedEndTime = "20:00";

            reservationTimeStart.disabled = true;

            function populateTimeOptions() {
                const times = [];
                let currentTime = 9;
                times.push(<?php echo json_encode(SELECT_TIME); ?>);

                while (currentTime <= 20) {
                    const timeString = (currentTime < 10 ? '0' : '') + currentTime + ":00";
                    times.push(timeString);
                    currentTime++;
                }

                times.forEach((time, index) => {
                    const startOption = document.createElement("option");
                    startOption.value = time;
                    startOption.textContent = time;

                    if (index === 0) {
                        startOption.textContent = <?php echo json_encode(SELECT_TIME); ?>;
                        startOption.selected = true;
                        startOption.disabled = true;
                        startOption.hidden = true;
                    }

                    reservationTimeStart.appendChild(startOption);
                });
            }

            populateTimeOptions();

            reservationDate.addEventListener("change", async function () {
                const selectedDate = reservationDate.value;

                if (selectedDate <= today) {
                    alert("You cannot select a past date.");
                    reservationDate.value = '';
                    reservationTimeStart.disabled = true;
                    return;
                }

                reservationTimeStart.value = <?php echo json_encode(SELECT_TIME); ?>;
                reservationTimeStart.disabled = true;

                const response = await fetch(`check_availability.php?date=${selectedDate}&veterinarianId=${veterinarianId}`);
                const data = await response.json();
                data.reservedTimes = undefined;

                if (data.isFullyBooked) {
                    alert("This date is fully booked. Please select another date.");
                    reservationDate.value = '';
                    reservationTimeStart.disabled = true;
                } else {
                    reservationTimeStart.disabled = false;
                    const reservedTimes = data.reservedTimes;

                    Array.from(reservationTimeStart.options).forEach(option => {
                        if (reservedTimes.includes(option.value)) {
                            option.disabled = true;
                            option.hidden = true;
                        } else {
                            option.disabled = false;
                            option.hidden = option.textContent === <?php echo json_encode(SELECT_TIME); ?>;
                        }
                    });
                }
            });

            reservationTimeStart.addEventListener("change", function () {
                const startTime = reservationTimeStart.value;

                if (startTime && startTime >= allowedStartTime && startTime <= allowedEndTime) {
                    let endHour = parseInt(startTime.split(":")[0]) + 1;
                    if (endHour > 20) endHour = 20;

                    const endTime = (endHour < 10 ? '0' : '') + endHour + ":00";
                    document.querySelector('[name="reservationTimeEnd"]').value = endTime;
                }
            });
        });
    </script>
</head>

<body class="container py-4" style="background: #659df7">
<a href="book_veterinarian.php" class="btn btn-success mt-4"><?php echo BACK_TO_VET_SELECTION;?></a>
<?php if($_SESSION['privilage']=="Veterinarian")
    echo '<h2 class="text-center mb-4">'.RESERVED_APOINTMENT_TITLE.' '.$_SESSION['userMail'].'</h2>';
else {
    $sql="SELECT veterinarianMail FROM veterinarian WHERE veterinarianId=:veterinarianId";
    $sql=$pdo->prepare($sql);
    $sql->bindValue(':veterinarianId',$_GET['veterinarian']);
    $sql->execute();
    $result=$sql->fetch();

    echo '<h2 class="text-center mb-4">' . RESERVED_APOINTMENT_TITLE . ' ' . $result['veterinarianMail'] . '</h2>';
}?>

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
    else
        echo ' <input type="hidden" name="veterinarianId" value="'.$_GET['veterinarian'].'">';
    ?>


    <button type="submit" class="btn btn-primary w-100"><?php echo RESERVE;?></button>
</form>

<h3 class="mt-5"><?php echo RESERVED_PETS_TITLE; ?></h3>
<div class="profile-section">
    <?php foreach ($reservedPets as $reservedPet): ?>
        <div class="pet-card">
            <label>
                <img alt="Pet Picture" src="pictures/<?= htmlspecialchars($reservedPet['petPicture']) ?>">
                <p class="pet-details"> <?php echo RESERVED_VETERINARIAN; ?> </p>
                <p class="pet-details"> <?= htmlspecialchars($reservedPet['veterinarianMail']) ?> </p>
                <p class="pet-details"> <?= htmlspecialchars($reservedPet['petName']) ?> </p>
                <p> <?= htmlspecialchars($reservedPet['reservationDay']) ?> </p>
                <p> <?= htmlspecialchars($reservedPet['reservationTime']) . "-" . htmlspecialchars($reservedPet['period']) ?> </p>
                <?php if($_SESSION['privilage']!="Veterinarian")
               echo ' <form method="post" action="functions.php">
                    <input type="hidden" value="',$reservedPet['reservationId'].'" name="reservationId">
                    <input type="hidden" value="deleteReservation" name="action">
                  
                         <input type="hidden" value="'. $_GET['veterinarian'] .'" name="veterinarian">
                    <input type="hidden" value="'. $_SESSION['userId'] .'" name="veterinarian">
                    
                    <input type="submit" value="'.DELETE_RESERVATION_BUTTON.'" class="btn btn-danger btn-sm" onclick="confirmDeletingApointment(event)">
                </form>'; ?>
            </label>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
