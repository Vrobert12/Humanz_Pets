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

