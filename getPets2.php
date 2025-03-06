<?php
include 'config.php';

$host = "localhost";
$dbname = "pets";
$username = "root";
$password = "";

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check the request method
$method = strtolower($_SERVER["REQUEST_METHOD"]);

if($method == "get") {

    $id = $_GET['id'] ?? null;

    $stmt = $pdo->prepare("SELECT petName FROM pet WHERE userId = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

}