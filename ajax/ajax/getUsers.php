<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");

ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../includes/config.php';
require '../includes/functions.php';

$json_data = [
    "draw" => 1,
    "data" => getUsers()
];

echo json_encode($json_data);