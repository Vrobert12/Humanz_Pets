<?php
// check_availability.php

// Get the date and veterinarianId from the request
$date = $_GET['date'];
$veterinarianId = $_GET['veterinarianId'];

// Database connection
require 'vendor/autoload.php';
include "functions.php";
$functions = new Functions();
$pdo = $functions->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);

// Query to get the reserved times for the selected date and veterinarian
$query = "
        SELECT DATE_FORMAT(r.reservationTime, '%H:%i') AS reservationTime
    FROM reservation r
    WHERE r.reservationDay = :date
    AND r.veterinarianId = :veterinarianId
";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':date', $date);
$stmt->bindParam(':veterinarianId', $veterinarianId);
$stmt->execute();

// Fetch the reserved times
$reservedTimes = $stmt->fetchAll(PDO::FETCH_COLUMN);

// If there are any reserved times, return them as an array
echo json_encode([
    'reservedTimes' => $reservedTimes,
    'isFullyBooked' => count($reservedTimes) >= 12
]);
exit; // Fontos, hogy ne fusson tovább a kód

