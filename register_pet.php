<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$dsn = "mysql:host=localhost;dbname=pets;charset=utf8mb4";
$db_user = "root";
$db_pass = "";

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()]));
}

$user_id = $_POST['user_id'] ?? null;
$name = $_POST['name'] ?? null;
$breed = $_POST['breed'] ?? null;
$species = $_POST['species'] ?? null;
$image_path = '';

if (!$user_id || !$name || !$breed || !$species) {
    die(json_encode(["success" => false, "message" => "Missing required fields"]));
}

if (isset($_FILES['image'])) {
    $target_dir = __DIR__ . "/pictures/"; // ✅ Ensure this folder exists
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true); // ✅ Create directory if it doesn't exist
    }

    $file_name = time() . "_" . basename($_FILES["image"]["name"]); // ✅ Prevent overwriting
    $image_path = $file_name; // ✅ Relative path for database
    $full_path = $target_dir . $file_name; // ✅ Absolute path for moving the file

    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $full_path)) {
        die(json_encode(["success" => false, "message" => "Failed to save the image"]));
    }
}

try {
    $stmt = $pdo->prepare("INSERT INTO pet (userId, petName, bred, petSpecies, profilePic) VALUES (:user_id, :name, :breed, :species, :image_path)");
    $stmt->execute([
        ':user_id' => $user_id,
        ':name' => $name,
        ':breed' => $breed,
        ':species' => $species,
        ':image_path' => $image_path,
    ]);

    echo json_encode(["success" => true, "message" => "Pet registered successfully", "image" => $image_path]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Failed to register pet: " . $e->getMessage()]);
}
