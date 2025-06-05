<?php
include 'config.php';
include 'functions.php';
$autoload = new Functions();
$pdo = $autoload->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);

header("Content-type: application/json; charset=UTF-8");

// Ensure the request method is POST
if (strtolower($_SERVER["REQUEST_METHOD"]) !== "post") {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        "message" => "Method not allowed. Only POST requests are allowed.",
        "status" => 405,
    ]);
    exit;
}

try {

    // Get the raw JSON input
    $input = file_get_contents('php://input');

    // Decode the JSON into a PHP associative array
    $data = json_decode($input, true);

    // Validate and process the data
    if (!empty($data['productName']) && !empty($data['productPicture']) && !empty($data['description']) && !empty($data['productCost'])) {
        $productName = $data['productName'];
        $productPicture = $data['productPicture'];
        $description = $data['description'];
        $productCost = $data['productCost'];

        // Insert the data into the database
        $stmt = $pdo->prepare("
            INSERT INTO products (productName, productPicture, description, productCost) 
            VALUES (:productName, :productPicture, :description, :productCost)
        ");
        $stmt->bindParam(':productName', $productName);
        $stmt->bindParam(':productPicture', $productPicture);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':productCost', $productCost);

        if ($stmt->execute()) {
            // Success response
            http_response_code(201); // Created
            echo json_encode([
                "message" => "Product created successfully.",
                "status" => 201,
                "data" => [
                    "productName" => $productName,
                    "productPicture" => $productPicture,
                    "description" => $description,
                    "productCost" => $productCost,
                ],
            ]);
        } else {
            // Failed to insert data
            http_response_code(500); // Internal Server Error
            echo json_encode([
                "message" => "Failed to insert product into the database.",
                "status" => 500,
            ]);
        }
    } else {
        // Invalid input response
        http_response_code(400); // Bad Request
        echo json_encode([
            "message" => "Invalid input. All fields are required.",
            "status" => 400,
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "message" => "Database connection failed: " . $e->getMessage(),
        "status" => 500,
    ]);
    exit;
}
