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


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['userId'])) {
    $userId = $_GET['userId'];

    try {
        $stmt = $pdo->prepare("SELECT upr.*, p.productName, p.productPicture, p.productCost 
                           FROM user_product_relation upr
                           JOIN product p ON upr.productId = p.productId
                           WHERE upr.userId = :userId AND upr.productPayed = 1");
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        $purchasedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($purchasedProducts);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}


