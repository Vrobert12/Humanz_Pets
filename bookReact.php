<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);

    $selectedPetId = $data['petId'] ?? null;
    $reservationDate = $data['day'] ?? null;
    $reservationStart = $data['reservationTimeStart'] ?? null;
    $reservationEnd = $data['reservationTimeEnd'] ?? null;

    if ($selectedPetId && $reservationDate && $reservationStart && $reservationEnd) {
        // Database logic goes here...

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


