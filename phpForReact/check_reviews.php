<?php

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
    // Create a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to get the reviews and count the pending reviews
    $stmt = $pdo->prepare("SELECT r.reviewId, r.reviewTime, CONCAT(v.firstName, ' ', v.lastName) AS veterinarian_name 
                           FROM review r
                           JOIN veterinarian v ON r.veterinarianId = v.veterinarianId
                           WHERE r.userId = :userId AND r.review IS NULL");

    $stmt->bindParam(":userId", $user_id, PDO::PARAM_STR);
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count pending reviews for the user
    $stmt = $pdo->prepare("SELECT userId,COUNT(reviewId) AS ReviewCount FROM review WHERE userId = :userId AND review IS NULL");
    $stmt->bindParam(":userId", $user_id, PDO::PARAM_STR);
    $stmt->execute();
    $reviewCount = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return both reviews and the review count
    echo json_encode([
        "reviews" => $reviews,
        "ReviewCount" => $reviewCount['ReviewCount']
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
