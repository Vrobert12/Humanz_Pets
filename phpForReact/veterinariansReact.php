<?php
global $pdo;
require_once 'react_config.php';

error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');


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


