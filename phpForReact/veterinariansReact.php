<?php
require_once 'config.php';

error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

$host = "localhost";
$dbname = "pets";
$username = "root";
$password = "";

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    //$stmt = $pdo->prepare("SELECT veterinarianId AS veterinarianId, firstName AS firstName, lastName AS lastName FROM veterinarian");
    $stmt = $pdo->prepare("SELECT veterinarianId, firstName, lastName FROM veterinarian");
    $stmt->execute();
    $veterinarians = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //echo json_encode(["vetId" => $veterinarians['veterinarianId'], "firstName" => $veterinarians['firstName'], "lastName" => $veterinarians['lastName']]);
    echo json_encode($veterinarians);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch veterinarians.']);
}


