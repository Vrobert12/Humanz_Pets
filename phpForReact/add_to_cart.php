<?php
require_once '../config.php'; // Include database connection

header('Content-Type: application/json');

$host = "localhost";
$dbname = "pets";
$username = "root";
$password = "";


$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['userId'], $data['productName'], $data['productPicture'], $data['productId'], $data['sum'], $data['price'], $data['productPayed'], $data['boughtDay'])) {
    echo json_encode(["error" => "Missing required parameters"]);
    exit;
}

$userId = $data['userId'];
$productName = $data['productName'];
$productPicture = $data['productPicture'];
$productId = $data['productId'];
$sum = $data['sum'];
$price = $data['price'];
$productPayed = $data['productPayed'];
$boughtDay = $data['boughtDay'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("INSERT INTO user_product_relation (userId, productName, productPicture, productId, sum, price, productPayed, boughtDay) VALUES (:userId, :productName, :productPicture, :productId, :sum, :price, :productPayed, :boughtDay)");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':productName', $productName, PDO::PARAM_STR);
    $stmt->bindParam(':productPicture', $productPicture, PDO::PARAM_STR);
    $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
    $stmt->bindParam(':sum', $sum, PDO::PARAM_INT);
    $stmt->bindParam(':price', $price, PDO::PARAM_STR);
    $stmt->bindParam(':productPayed', $productPayed, PDO::PARAM_INT);
    $stmt->bindParam(':boughtDay', $boughtDay, PDO::PARAM_STR);
    $stmt->execute();
    echo json_encode(["message" => "Product added to cart successfully"]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}

