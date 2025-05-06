<?php
global $pdo;
require_once 'react_config.php';

header("Content-Type: application/json");

ini_set('display_errors', 1);
error_reporting(E_ALL);


// Ensure the correct HTTP method
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => 405, "message" => "Method Not Allowed"]);
    exit();
}

// Read raw input stream
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Validate data
if (!$data || !isset($data["id"]) || !isset($data["firstName"]) || !isset($data["lastName"]) || !isset($data["phoneNumber"]) || !isset($data["usedLanguage"])) {
    echo json_encode(["status" => 400, "message" => "Invalid request body, missing required fields"]);
    exit();
}

$userId = $data["id"];
$firstName = $data["firstName"];
$lastName = $data["lastName"];
$phoneNumber = $data["phoneNumber"];
$language = $data["usedLanguage"];

try {
    // Prepare SQL query to update user information
    $stmt = $pdo->prepare("UPDATE user SET firstName = :firstName, lastName = :lastName, phoneNumber = :phoneNumber, usedLanguage = :language WHERE userId = :userId");
    $stmt->execute([
        ":firstName" => $firstName,
        ":lastName" => $lastName,
        ":phoneNumber" => $phoneNumber,
        ":language" => $language,
        ":userId" => $userId
    ]);

    echo json_encode(["status" => 200, "message" => "User updated successfully"]);
} catch (PDOException $e) {
    echo json_encode(["status" => 500, "message" => "Database error: " . $e->getMessage()]);
}
