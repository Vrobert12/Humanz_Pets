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
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);

    $pet_id = $_POST['pet_id'] ?? null;
    $date = $_POST['date'] ?? null;
    $start = $_POST['start'] ?? null;
    $end = $_POST['end'] ?? null;
    $veterinarianId = $_POST['vet_id'] ?? null;

    if ($pet_id && $date && $start && $end) {
        // Check if the requested time slot is already taken
        $checkQuery = $pdo->prepare(
            "SELECT COUNT(*) FROM reservation 
             WHERE reservationDay = :reservationDay 
             AND reservationTime = :reservationStart
             AND period = :reservationEnd"
        );
        $checkQuery->execute([
            ':reservationDay' => $date,
            ':reservationStart' => $start,
            ':reservationEnd' => $end
        ]);

        $existingReservations = $checkQuery->fetchColumn();

        // If there is an existing reservation for that time, return an error
        if ($existingReservations > 0) {
            $response = [
                'message' => 'The selected time slot is already booked. Please choose another time.'
            ];
            echo json_encode($response);
            exit; // Exit after returning the response to avoid inserting the reservation
        }

        // If the time slot is available, insert the new reservation
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

        // If everything is okay
        $response = [
            'message' => 'Reservation successful!'
        ];
        echo json_encode($response);

    } else {
        // Handle errors
        $response = [
            'message' => 'All fields are required.'
        ];
        echo json_encode($response);
    }
} else {
    // If not POST request
    http_response_code(405);
    $response = ['message' => 'Method Not Allowed'];
    echo json_encode($response);
}
