<?php
include 'config.php';

header("Content-type: application/json; charset=UTF-8");

$table = $_GET['table'] ?? "";

if(strtolower($_SERVER["REQUEST_METHOD"]) == "get" && !empty($table)) {
    $message = "Statistics data fetched successfully. $table";
    $status = 200;
    http_response_code(200);
}
else {
    $message = "Method is not allowed or parameter is missing!";
    $status = 405;
    http_response_code(405);
}

// Adatbázis kapcsolat
$host = "localhost";
$dbname = "pets";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!empty($table)) {
        // Lekérdezés
        $stmt = $pdo->query("SELECT * FROM $table");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $message = "Data fetched successfully.";
        $status = 200;
        http_response_code(200);

        echo json_encode([
            "message" => $message,
            "status" => $status,
            "data" => $data,
        ]);
        exit;
    }
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "message" => "Database connection failed: " . $e->getMessage(),
        "status" => 500,
    ]);
    exit;
}