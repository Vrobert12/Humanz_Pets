<?php
require_once 'config.php'; // Adjust to your database config

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method"]);
    exit;
}

$host = "localhost";
$dbname = "pets";
$username = "root";
$password = "";

$data = json_decode(file_get_contents("php://input"), true);
$review_id = $data['review_id'] ?? null;
$rating = $data['rating'] ?? null;

if (!$review_id || $rating === null) {
    echo json_encode(["error" => "Review ID and rating are required"]);
    exit;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("UPDATE review SET review = ? WHERE reviewId = ?");
    $stmt->execute([$rating, $review_id]);

    echo json_encode(["success" => "Review updated"]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}

