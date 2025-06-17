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
    $userId = $data['user_id'] ?? null;

    if ($userId) {
        $query = $pdo->prepare("
            SELECT r.reservationId, p.petName, v.veterinarianMail AS vetEmail, r.reservationDay, r.reservationTime, r.period
            FROM reservation r
            INNER JOIN pet p ON r.petId = p.petId
            INNER JOIN veterinarian v ON r.veterinarianId = v.veterinarianId
            WHERE p.userId = :userId
            ORDER BY r.reservationDay DESC
        ");
        $query->execute([':userId' => $userId]);

        $reservations = $query->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['reservations' => $reservations]);
    } else {
        echo json_encode(['message' => 'User ID is required.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
}

