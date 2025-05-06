<?php
global $pdo;
require_once 'react_config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");


$user_id = $_POST['user_id'] ?? null;
$vet_id = $_POST['veterinarian_id'] ?? null;
$name = $_POST['name'] ?? null;
$breed = $_POST['breed'] ?? null;
$species = $_POST['species'] ?? null;
$image_path = '';

if (!$user_id || !$name || !$breed || !$species) {
    die(json_encode(["success" => false, "message" => "Missing required fields"]));
}

if (isset($_FILES['image'])) {
    // Move back one directory level and then navigate to the pictures folder
    $target_dir = __DIR__ . "/../pictures/"; // Going one level up

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);  // Create the directory if it doesn't exist
    }

    $image_name = pathinfo($_FILES["image"]["name"], PATHINFO_FILENAME) . ".png"; // Force .png extension
    $image_path = $target_dir . $image_name;
    $image_url = "http://192.168.1.8/Humanz2.0/Humanz_Pets/pictures/" . $image_name;

    // Get MIME type of uploaded image
    $image_info = getimagesize($_FILES["image"]["tmp_name"]);
    $image_mime = $image_info["mime"];

    // Convert image to PNG
    if ($image_mime == "image/jpeg") {
        $image = imagecreatefromjpeg($_FILES["image"]["tmp_name"]);
    } elseif ($image_mime == "image/png") {
        $image = imagecreatefrompng($_FILES["image"]["tmp_name"]);
    } else {
        die(json_encode(["success" => false, "message" => "Unsupported file format"]));
    }

    // Save as PNG
    if (!imagepng($image, $image_path)) {
        die(json_encode(["success" => false, "message" => "Failed to save image as PNG"]));
    }

    imagedestroy($image); // Free memory

    // Insert into database
    try {
        $stmt = $pdo->prepare("INSERT INTO pet (userId, petName, bred, petSpecies, profilePic, veterinarId) 
                               VALUES (:user_id, :name, :breed, :species, :image_name, :vetId)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':name' => $name,
            ':breed' => $breed,
            ':species' => $species,
            ':vetId' => $vet_id,
            ':image_name' => $image_name,
        ]);
        echo json_encode(["success" => true, "message" => "Pet registered successfully"]);
        exit;
    } catch (PDOException $e) {
        die(json_encode(["success" => false, "message" => "Database insert failed: " . $e->getMessage()]));
    }

} else {
    die(json_encode(["success" => false, "message" => "No image uploaded"]));
}
