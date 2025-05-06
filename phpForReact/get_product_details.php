<?php
global $pdo;
require_once 'react_config.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(["error" => "Product ID is required"]);
    exit;
}

$productId = intval($_GET['id']);


try {
    $stmt = $pdo->prepare("SELECT productId, productName, productPicture, productCost, description, productRelease FROM product WHERE productId = :productId");
    $stmt->bindParam(":productId", $productId);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        echo json_encode($product);
    } else {
        echo json_encode(["error" => "Product not found"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>

