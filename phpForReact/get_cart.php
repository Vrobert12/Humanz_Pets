<?php

$host = "localhost";
$dbname = "pets";
$username = "root";
$password = "";

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$userId = $_GET['userId'];
$stmt = $pdo->prepare("SELECT * FROM user_product_relation WHERE userId = :userId AND productPayed = 0");
$stmt->bindParam(":userId", $userId);
$stmt->execute();
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

