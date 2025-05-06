<?php
global $pdo;
require_once 'react_config.php';

header('Content-Type: application/json');


try {
    $stmt = $pdo->query("SELECT productId, productName, productPicture, productCost FROM product");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($products);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}


