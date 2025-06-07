<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
global $pdo;
require_once 'react_config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);


$response = ["success" => false, "message" => "Invalid request"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    if (isset($input['id'])) {
        $id = intval($input['id']);

        $stmt = $pdo->prepare("DELETE FROM user_product_relation WHERE userProductRelationId = ?");
        if ($stmt->execute([$id])) {
            $response = ["success" => true, "message" => "Item deleted"];
        } else {
            $response = ["success" => false, "message" => "Failed to delete item"];
        }
    }
}

echo json_encode($response);

