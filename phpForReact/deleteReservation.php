<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
global $pdo;
require_once 'react_config.php';



if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $reservationId = $data['reservationId'] ?? null;

    if (!$reservationId) {
        echo json_encode(['message' => 'Reservation ID is required']);
        exit;
    }

    // Get the reservation details
    $stmt = $pdo->prepare("SELECT reservationDay, reservationTime FROM reservation WHERE reservationId = :reservationId");
    $stmt->execute([':reservationId' => $reservationId]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        echo json_encode(['message' => 'Reservation not found']);
        exit;
    }

    $reservationDateTime = new DateTime($reservation['reservationDay'] . ' ' . $reservation['reservationTime']);
    $currentDateTime = new DateTime();
    $interval = $currentDateTime->diff($reservationDateTime);

    // Prevent deletion if the reservation is in 1 hour or less
    if ($reservationDateTime <= $currentDateTime && ($interval->h == 0 && $interval->i <= 60)) {
        echo json_encode(['message' => 'Cannot delete reservation within 1 hour of appointment.']);
        exit;
    }

    // Delete the reservation
    $deleteStmt = $pdo->prepare("DELETE FROM reservation WHERE reservationId = :reservationId");
    $deleteStmt->execute([':reservationId' => $reservationId]);

    echo json_encode(['message' => 'Reservation deleted successfully']);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
}
