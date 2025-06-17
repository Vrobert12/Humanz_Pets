<?php
header('Content-Type: application/json; charset=utf-8');
require '../includes/config.php';
require '../includes/functions.php';

$json_data = [
    "draw" => 1,
    "data" => getRatings()
];

echo json_encode($json_data);