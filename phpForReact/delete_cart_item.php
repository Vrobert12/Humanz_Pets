<?php
global $pdo;
require_once 'react_config.php';

header('Content-Type: application/json');
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

