<?php
header('Content-Type: application/json');

include 'config.php';

$host = "localhost";
$dbname = "pets";
$username = "root";
$password = "";

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $pet_id = $_POST['pet_id'] ?? null;
    $date = $_POST['date'] ?? null;
    $start = $_POST['start'] ?? null;
    $end = $_POST['end'] ?? null;
    $veterinarianId = $_POST['veterinarianId'] ?? null;

    if ($pet_id && $date && $start && $end && $veterinarianId) {

        // Check if the pet already has an overlapping reservation with the same veterinarian
        $checkOverlapQuery = $pdo->prepare(
            "SELECT COUNT(*) 
             FROM reservation 
             WHERE petId = :petId 
             OR reservationDay = :date 
             AND veterinarianId = :veterinarianId
             AND (
                 (reservationTime BETWEEN :start AND :end) 
                 OR (reservationTime < :start AND period > :end)
             )"
        );
        $checkOverlapQuery->execute([
            ':petId' => $pet_id,
            ':date' => $date,
            ':start' => $start,
            ':end' => $end,
            ':veterinarianId' => $veterinarianId
        ]);
        $existingReservationCount = $checkOverlapQuery->fetchColumn();

        if ($existingReservationCount > 0) {
            echo json_encode(['message' => 'This pet/veterinarian already has an appointment at the selected time.']);
            exit;
        }

        // Check if the selected time slot is already taken by another veterinarian for the same pet
        $checkTimeQuery = $pdo->prepare(
            "SELECT COUNT(*) 
             FROM reservation 
             WHERE petId = :petId
             AND reservationDay = :date 
             AND (
                 (reservationTime BETWEEN :start AND :end) 
                 OR (reservationTime < :start AND period > :start)
             )"
        );
        $checkTimeQuery->execute([
            ':date' => $date,
            ':start' => $start,
            ':end' => $end,
            ':petId' => $pet_id
        ]);
        $existingTimeCount = $checkTimeQuery->fetchColumn();

        if ($existingTimeCount > 0) {
            echo json_encode(['message' => 'This pet already has an appointment at the selected time.']);
            exit;
        }

        // Insert the new reservation
        $insertQuery = $pdo->prepare(
            "INSERT INTO reservation (petId, veterinarianId, reservationDay, reservationTime, period) 
             VALUES (:petId, :veterinarianId, :reservationDay, :reservationStart, :reservationEnd)"
        );
        $insertQuery->execute([
            ':petId' => $pet_id,
            ':veterinarianId' => $veterinarianId,
            ':reservationDay' => $date,
            ':reservationStart' => $start,
            ':reservationEnd' => $end
        ]);

        echo json_encode(['message' => 'Reservation successful!']);
    } else {
        echo json_encode(['message' => 'All fields are required.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
}
?>
