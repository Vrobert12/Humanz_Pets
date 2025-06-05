<?php
include 'config.php';
include 'functions.php';
$autoload = new Functions();
$pdo = $autoload->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);

// Check the request method
$method = strtolower($_SERVER["REQUEST_METHOD"]);

if($method == "get") {

    $id = $_GET['id'] ?? null;

    $stmt = $pdo->prepare("SELECT petName FROM pet WHERE userId = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

}