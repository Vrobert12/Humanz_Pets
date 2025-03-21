<?php

$host = "localhost";
$dbname = "pets";
$username = "root";
$password = "";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['userId'])) {
    $userId = $_GET['userId'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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


