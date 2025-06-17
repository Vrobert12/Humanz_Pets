<?php
include "functions.php";
$autoload = new Functions();
$lang = $autoload->language();
$autoload->checkAutoLogin();

if ($_SESSION['privilage'] != 'Veterinarian') {
    header('location: index.php');
    exit();
}

$veterinarianId = $_SESSION['userId'];
$pdo = $autoload->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);

$petQuery = "SELECT u.userMail,u.userId,u.usedLanguage,p.petId, p.petName, p.bred, p.petSpecies, p.profilePic, r.reservationDay, r.reservationTime, r.period,r.reservationId
FROM pet p
LEFT JOIN reservation r ON p.petId = r.petId 
INNER JOIN user u ON p.userId = u.userId
WHERE r.veterinarianId = :veterinarianId 
AND r.animalChecked = 0
AND (r.reservationDay IS NOT NULL AND r.reservationDay >= CURDATE()) 
ORDER BY r.reservationDay ASC, r.reservationTime ASC";

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
    <title>Reserved Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/booked_users.css">
    
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a class="btn btn-secondary" href="index.php"><?php echo BACK ?></a>
        <h2 class="mb-0"><?php echo NAV_BOOK_VETERINARIAN ?></h2>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success" id="popupMessage">
            <?php echo $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- Changed part starts here -->
    <?php if (count($reservedPets) > 0): ?>
        <div class="row g-4">
            <?php foreach ($reservedPets as $reservedPet):
                $language = $reservedPet['usedLanguage'] == 'hu' ? LANGUAGE_hu :
                    ($reservedPet['usedLanguage'] == 'sr' ? LANGUAGE_sr : LANGUAGE_en);
                $_SESSION['backPic'] = 'booked_users.php';
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column align-items-center text-center">

                            <a class="btn btn-success btn-sm mb-2"
                               href="petsInfo.php?petId=<?= htmlspecialchars($reservedPet['petId']) ?>">
                                <?php echo DETAILS ?>
                            </a>

                            <img src="pictures/<?= htmlspecialchars($reservedPet['profilePic']) ?>"
                                 alt="Pet Picture"
                                 class="rounded-circle mb-3"
                                 style="width: 120px; height: 120px; object-fit: cover; border: 2px solid #007bff;">

                            <h5 class="mb-1"><?= htmlspecialchars($reservedPet['petName']) ?></h5>
                            <p class="mb-1 text-muted small"><?= htmlspecialchars($reservedPet['userMail']) ?></p>
                            <p class="mb-1 small"><?= $language ?></p>
                            <p class="mb-1"><strong><?= htmlspecialchars($reservedPet['reservationDay']) ?></strong></p>
                            <p class="text-muted small"><?= htmlspecialchars($reservedPet['reservationTime']) . " - " . htmlspecialchars($reservedPet['period']) ?></p>

                            <form method="post" action="functions.php" class="w-100 mt-3 text-start">
    <label for="mailText" class="form-label small fw-bold">Reason for deleting reservation:</label>
    <input type="hidden" name="reservationId" value="<?= $reservedPet['reservationId'] ?>">
    <input type="hidden" name="action" value="deleteReservationByVet">
    <input type="hidden" name="cancelEmail" value="<?= htmlspecialchars($reservedPet['userMail']) ?>">
    <input type="hidden" name="ownerLanguage" value="<?= htmlspecialchars($reservedPet['usedLanguage']) ?>">
    <textarea name="mailText" class="form-control form-control-sm mb-2" rows="2" required></textarea>
    <input type="submit" value="Delete" class="btn btn-danger btn-sm w-100" onclick="confirmDeletingApointment(event)">
</form>

<form method="post" action="functions.php" class="w-100 mt-2">
    <input type="hidden" name="reservationId" value="<?= htmlspecialchars($reservedPet['reservationId']) ?>">
    <input type="hidden" name="ownerMail" value="<?= htmlspecialchars($reservedPet['userMail']) ?>">
    <input type="hidden" name="ownerId" value="<?= htmlspecialchars($reservedPet['userId']) ?>">
    <input type="hidden" name="action" value="animalChecked">
    <input type="submit" value="Check" class="btn btn-primary btn-sm w-100" onclick="confirmCheck(event)">
</form>


                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center fs-5 mt-4">
            <?php echo NORES?>
        </div>
    <?php endif; ?>
    <!-- Changed part ends here -->
</div>

<script>
    window.onload = function () {
        const popup = document.getElementById('popupMessage');
        if (popup) {
            setTimeout(() => popup.style.display = 'none', 5000);
        }
    };

    function confirmDeletingApointment(event) {
        if (!confirm("Are you sure you want to delete this reservation?")) {
            event.preventDefault();
        }
    }

    function confirmCheck(event) {
        if (!confirm("Mark this pet as checked?")) {
            event.preventDefault();
        }
    }
</script>
</body>
</html>
