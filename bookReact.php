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
    $veterinarianId = 3;

    if ($pet_id && $date && $start && $end) {
        // Database logic goes here...

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


