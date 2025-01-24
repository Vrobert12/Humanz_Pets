<?php

include "functions.php";
$autoload = new Functions();
$lang = $autoload->language();
include "lang_$lang.php";

$pdo = $autoload->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);

$sql = "SELECT reviewCode, reviewTime FROM review";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll();

$valid = false;
$reviewTime = "";

// Check if the reviewCode exists and get the corresponding reviewTime
foreach ($result as $row) {
    if ($row['reviewCode'] == $_GET['reviewCode']) {
        $valid = true;
        $reviewTime = $row['reviewTime'];
        break; // Stop the loop once we've found the matching reviewCode
    }
}

if (!$valid) {
    // If the reviewCode doesn't exist, redirect to index
    header('Location: index.php');
    exit();
}

// Check if the review is older than 24 hours
$reviewDate = new DateTime($reviewTime);
$currentDate = new DateTime();

// Calculate the total difference in hours
$interval = $currentDate->diff($reviewDate);
$totalHours = ($interval->days * 24) + $interval->h; // Convert days to hours and add the hours part

// If the review is older than 24 hours, redirect to index
if ($totalHours >= 71) {
    header('Location: index.php');
    exit();
}


