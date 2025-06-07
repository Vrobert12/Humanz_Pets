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


$userId = $_GET['userId'];
$stmt = $pdo->prepare("SELECT * FROM user_product_relation WHERE userId = :userId AND productPayed = 0");
$stmt->bindParam(":userId", $userId);
$stmt->execute();
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

