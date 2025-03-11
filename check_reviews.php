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
$user_id = $data['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(["error" => "User ID is required"]);
    exit;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("SELECT r.reviewId, v.firstName AS veterinarian_name 
                           FROM review r
                           JOIN veterinarian v ON r.veterinarianId = v.veterinarianId
                           WHERE r.userId = :userId AND r.review IS NULL");
    $stmt->bindParam(":userId", $user_id, PDO::PARAM_STR);
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["reviews" => $reviews]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}


