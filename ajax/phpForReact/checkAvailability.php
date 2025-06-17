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
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);
    $date = $data['date'] ?? null;

    if ($date) {
        // Get all existing reservations for the selected date
        $query = $pdo->prepare(
            "SELECT reservationTime, period FROM reservation WHERE reservationDay = :reservationDay"
        );
        $query->execute([':reservationDay' => $date]);

        $existingReservations = $query->fetchAll(PDO::FETCH_ASSOC);

        // Generate a list of available start times
        $availableStartTimes = [];
        $allStartTimes = [
            '08:00:00', '09:00:00', '10:00:00', '11:00:00', '12:00:00', '13:00:00', '14:00:00', '15:00:00', '16:00:00', '17:00:00', '18:00:00', '19:00:00', '20:00:00'
        ];

        // Exclude occupied start times
        foreach ($allStartTimes as $time) {
            $occupied = false;
            foreach ($existingReservations as $reservation) {
                if ($reservation['reservationTime'] === $time) {
                    $occupied = true;
                    break;
                }
            }
            if (!$occupied) {
                $availableStartTimes[] = $time;
            }
        }

        // Return available start times to the client
        echo json_encode(['availableStartTimes' => $availableStartTimes]);
    } else {
        echo json_encode(['message' => 'Date is required']);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
}
