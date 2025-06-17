<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

global $pdo;
require_once 'react_config.php';

// Read JSON input from request body
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['userId'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

$userId = intval($data['userId']);

$stmt = $pdo->prepare("SELECT * FROM user_product_relation WHERE userId = :userId AND productPayed = 0");
$stmt->bindParam(":userId", $userId);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results);
?>
