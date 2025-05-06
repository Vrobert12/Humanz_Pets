<?php
global $pdo;
require_once 'react_config.php';


$userId = $_GET['userId'];
$stmt = $pdo->prepare("SELECT * FROM user_product_relation WHERE userId = :userId AND productPayed = 0");
$stmt->bindParam(":userId", $userId);
$stmt->execute();
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

