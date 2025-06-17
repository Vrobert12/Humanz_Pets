<?php
global $pdo;
require_once 'react_config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'] ?? null;
$vet_id = $data['veterinarian_id'] ?? null;
$name = $data['name'] ?? null;
$breed = $data['breed'] ?? null;
$species = $data['species'] ?? null;
$image_base64 = $data['image_base64'] ?? null;

if (!$user_id || !$name || !$breed || !$species) {
    die(json_encode(["success" => false, "message" => "Missing required fields or image"]));
}

$target_dir = __DIR__ . "/../pictures/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$newFileName = date('YmdHis') . ".png";
$image_path = $target_dir . $newFileName;
$image_url = "https://humanz.stud.vts.su.ac.rs/pictures/" . $newFileName;

// Decode base64 string
$image_data = base64_decode($image_base64);
if ($image_data === false) {
    die(json_encode(["success" => false, "message" => "Failed to decode base64 image"]));
}

// Save the image as PNG file
if (file_put_contents($image_path, $image_data) === false) {
    die(json_encode(["success" => false, "message" => "Failed to save image file"]));
}

// Insert into DB
try {
    $stmt = $pdo->prepare("INSERT INTO pet (userId, petName, bred, petSpecies, profilePic, veterinarId) 
                           VALUES (:user_id, :name, :breed, :species, :image_name, :vetId)");
    $stmt->execute([
        ':user_id' => $user_id,
        ':name' => $name,
        ':breed' => $breed,
        ':species' => $species,
        ':vetId' => $vet_id,
        ':image_name' => $newFileName,
    ]);
    echo json_encode(["success" => true, "message" => "Pet registered successfully"]);
    exit;
} catch (PDOException $e) {
    die(json_encode(["success" => false, "message" => "Database insert failed: " . $e->getMessage()]));
}
