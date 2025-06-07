<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
global $pdo;
require_once 'react_config.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $pet_id = $_POST['pet_id'] ?? null;
    $date = $_POST['date'] ?? null;
    $todays_date = date("Y-m-d");
    $start = $_POST['start'] ?? null;
    $end = $_POST['end'] ?? null;
    $veterinarianId = $_POST['veterinarianId'] ?? null;

    if ($pet_id && $date && $start && $end && $veterinarianId) {

        $checkOverlapQuery = $pdo->prepare(
            "SELECT COUNT(*) 
     FROM reservation 
     WHERE 
         petId = :petId 
         AND (
             (reservationDay = :date 
             AND veterinarianId = :veterinarianId
             AND (
                 (reservationTime BETWEEN :start AND :end) 
                 OR (reservationTime < :start AND period > :end)
             ))
             OR reservationDay > :todays_date
         )"
        );

        $checkOverlapQuery->execute([
            ':petId' => $pet_id,
            ':date' => $date,
            ':todays_date' => $todays_date,  // Current date
            ':start' => $start,
            ':end' => $end,
            ':veterinarianId' => $veterinarianId
        ]);

        $existingReservationCount = $checkOverlapQuery->fetchColumn();

        if ($existingReservationCount > 0) {
            echo json_encode(['message' => 'This pet already has an appointment at the selected time or in the future.']);
            exit;
        }


// Check if the veterinarian already has an overlapping reservation at the selected time
        $checkVetOverlapQuery = $pdo->prepare(
            "SELECT COUNT(*) 
     FROM reservation 
     WHERE 
         veterinarianId = :veterinarianId 
         AND reservationDay = :date
         AND (
             (reservationTime BETWEEN :start AND :end) 
             OR (reservationTime < :start AND period > :end)
         )"
        );

        $checkVetOverlapQuery->execute([
            ':date' => $date,
            ':start' => $start,
            ':end' => $end,
            ':veterinarianId' => $veterinarianId
        ]);

        $existingReservationCount2 = $checkVetOverlapQuery->fetchColumn();

        if ($existingReservationCount2 > 0) {
            echo json_encode(['message' => 'This veterinarian already has an appointment at the selected time.']);
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
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Filter out pets with appointments today or the following days
    $currentDate = date('Y-m-d');
    $nextDate = date('Y-m-d', strtotime('+1 day'));

    // Query to get pets that don't have appointments for today or the following days
    $petsQuery = $pdo->prepare(
        "SELECT p.petId, p.petName 
         FROM pet p
         WHERE NOT EXISTS (
             SELECT 1 FROM reservation r
             WHERE r.petId = p.petId
             AND r.reservationDay >= :currentDate
             AND r.reservationDay <= :nextDate
         )"
    );
    $petsQuery->execute([
        ':currentDate' => $currentDate,
        ':nextDate' => $nextDate
    ]);

    $pets = $petsQuery->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($pets);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
}
?>
