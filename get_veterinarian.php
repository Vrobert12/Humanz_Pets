<?php
require 'vendor/autoload.php';
include "functions.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');


$functions = new Functions();
$pdo = $functions->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);

if (isset($_GET['petId'])) {
    $petId = $_GET['petId'];

    $sql = "SELECT v.veterinarianId FROM veterinarian v 
            INNER JOIN pet p ON v.veterinarianId = p.veterinarId 
            WHERE p.petId = :petId";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':petId', $petId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($result ?: []);
}
