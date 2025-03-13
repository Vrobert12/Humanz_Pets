<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$dbname = "pets";
$username = "root";
$password = "";

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    if (empty($_GET['user'])) {
        echo json_encode(["status" => 400, "message" => "User ID is required"]);
        exit();
    }

    $userId = intval($_GET['user']); // Sanitize input

    // Prepare and execute the query
    $sql = "SELECT qrCodeName FROM qr_code WHERE userId = :userId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        echo json_encode(["status" => 200, "data" => ["path" => $row['qrCodeName']]]);
    } else {
        echo json_encode(["status" => 404, "message" => "QR Code not found"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => 500, "message" => "Database error: " . $e->getMessage()]);
}

