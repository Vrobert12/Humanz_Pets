<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Invalid request method. Use POST."]);
    exit;
}

require_once 'react_config.php';
global $pdo;

$data = json_decode(file_get_contents("php://input"), true);

$review_id = $data['review_id'] ?? null;
$rating = $data['rating'] ?? null;

if (!$review_id || $rating === null) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Review ID and rating are required."]);
    exit;
}

// Validate rating is numeric and between 1 and 5 (inclusive)
if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(["error" => "Rating must be a number between 1 and 5."]);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE review SET review = :rating WHERE reviewId = :reviewId");
    $stmt->bindParam(':rating', $rating);
    $stmt->bindParam(':reviewId', $review_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["success" => "Review updated successfully."]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to update review."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
