<?php
global $pdo;
require_once 'react_config.php';

header("Content-type: application/json; charset=UTF-8");


try {

    // Check the request method
    $method = strtolower($_SERVER["REQUEST_METHOD"]);

    if ($method === "get") {
        // Handle GET requests
        $table = $_GET['table'] ?? "";
        $id = $_GET['id'] ?? null; // Optional parameter for a specific ID

        if (empty($table)) {
            http_response_code(400); // Bad Request
            echo json_encode([
                "message" => "Missing table parameter.",
                "status" => 400,
            ]);
            exit;
        }

        if (!empty($id)) {
            switch ($table) {
                case "user":
                    $stmt = $pdo->prepare("SELECT * FROM $table WHERE userId = :id");
                    break;
                case "veterinarian":
                    $stmt = $pdo->prepare("SELECT * FROM $table WHERE veterinarianId = :id");
                    break;
                case "pet":
                    $stmt = $pdo->prepare("SELECT * FROM $table WHERE petId = :id");
                    break;
                case "product":
                    $stmt = $pdo->prepare("SELECT * FROM $table WHERE productId = :id");
                    break;
                case "reservation":
                    $stmt = $pdo->prepare("SELECT * FROM $table WHERE reservationId = :id");
                    break;
                case "review":
                    $stmt = $pdo->prepare("SELECT * FROM $table WHERE reviewId = :id");
                    break;
                default:
                    http_response_code(400); // Bad Request
                    echo json_encode([
                        "message" => "Invalid table name.",
                        "status" => 400,
                    ]);
                    exit;
            }
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM $table");
        }

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($data)) {
            http_response_code(404); // Not Found
            echo json_encode([
                "message" => "No records found.",
                "status" => 404,
            ]);
            exit;
        }

        http_response_code(200); // OK
        echo json_encode([
            "message" => "Data fetched successfully.",
            "status" => 200,
            "data" => $data,
        ]);
    } elseif ($method === "post") {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if ($table == "product") {
            // Handle POST requests for product
            if (!empty($data['productName']) && !empty($data['productPicture']) && !empty($data['description']) && !empty($data['productCost'])) {
                $productName = $data['productName'];
                $productPicture = $data['productPicture'];
                $description = $data['description'];
                $productCost = $data['productCost'];

                $stmt = $pdo->prepare("
                INSERT INTO product (productName, productPicture, description, productCost) 
                VALUES (:productName, :productPicture, :description, :productCost)
            ");
                $stmt->bindParam(':productName', $productName);
                $stmt->bindParam(':productPicture', $productPicture);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':productCost', $productCost);

                if ($stmt->execute()) {
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
                    http_response_code(500); // Internal Server Error
                    echo json_encode([
                        "message" => "Failed to insert product into the database.",
                        "status" => 500,
                    ]);
                }
            } else {
                http_response_code(400); // Bad Request
                echo json_encode([
                    "message" => "Invalid input. All fields are required.",
                    "status" => 400,
                ]);
            }
        } elseif ($table == "review") {
            // Handle POST requests for review
            if (!empty($data['reviewCode']) && !empty($data['userId']) && !empty($data['veterinarianId']) && !empty($data['review'])) {
                $reviewCode = $data['reviewCode'];
                $userId = $data['userId'];
                $veterinarianId = $data['veterinarianId'];
                $review = $data['review'];

                $stmt = $pdo->prepare("
                INSERT INTO review (reviewCode, userId, veterinarianId, review) 
                VALUES (:reviewCode, :userId, :veterinarianId, :review)
            ");
                $stmt->bindParam(':reviewCode', $reviewCode);
                $stmt->bindParam(':userId', $userId);
                $stmt->bindParam(':veterinarianId', $veterinarianId);
                $stmt->bindParam(':review', $review);

                if ($stmt->execute()) {
                    http_response_code(201); // Created
                    echo json_encode([
                        "message" => "Review created successfully.",
                        "status" => 201,
                        "data" => [
                            "reviewCode" => $reviewCode,
                            "userId" => $userId,
                            "veterinarianId" => $veterinarianId,
                            "review" => $review
                        ],
                    ]);
                } else {
                    http_response_code(500); // Internal Server Error
                    echo json_encode([
                        "message" => "Failed to insert review into the database.",
                        "status" => 500,
                    ]);
                }
            } else {
                http_response_code(400); // Bad Request
                echo json_encode([
                    "message" => "Invalid input. All fields are required.",
                    "status" => 400,
                ]);
            }
        }
    } elseif ($method === "patch") {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (!empty($data['reviewCode']) && !empty($data['review'])) {
            $reviewCode = $data['reviewCode'];
            $review = $data['review'];

            // Check if the reviewCode exists in the database
            $stmt = $pdo->prepare("SELECT * FROM review WHERE reviewCode = :reviewCode");
            $stmt->bindParam(':reviewCode', $reviewCode);
            $stmt->execute();
            $existingReview = $stmt->fetch(PDO::FETCH_ASSOC);

            // If the reviewCode exists, update the review
            if ($existingReview) {
                $stmt = $pdo->prepare("UPDATE review SET review = :review WHERE reviewCode = :reviewCode");
                $stmt->bindParam(':reviewCode', $reviewCode);
                $stmt->bindParam(':review', $review);

                if ($stmt->execute()) {
                    http_response_code(200); // OK
                    echo json_encode([
                        "message" => "Review updated successfully.",
                        "status" => 200,
                        "data" => [
                            "review" => $review,
                            "reviewCode" => $reviewCode
                        ],
                    ]);
                } else {
                    http_response_code(500); // Internal Server Error
                    echo json_encode([
                        "message" => "Failed to update review in the database.",
                        "status" => 500,
                    ]);
                }
            } else {
                http_response_code(404); // Not Found
                echo json_encode([
                    "message" => "Review code not found.",
                    "status" => 404,
                ]);
            }
        }
        elseif (!empty($_GET['id']) && !empty($data['firstName']) && !empty($data['lastName'])
            && !empty($data['phoneNumber']) && !empty($data['usedLanguage'])) {

                    $Id = $_GET['id']; // The ID from the request body
                    $firstName = $data['firstName'];
                    $lastName = $data['lastName'];
                    $phoneNumber = $data['phoneNumber'];
                    $usedLanguage = $data['usedLanguage'];

                    // Check if user exists
                    $stmt = $pdo->prepare("SELECT * FROM user WHERE userId = :userId");
                    $stmt->bindParam(':userId', $Id);
                    $stmt->execute();
                    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($existingUser) {
                        // Update user details
                        $stmt = $pdo->prepare("UPDATE user SET firstName = :firstName, lastName = :lastName, 
                phoneNumber = :phoneNumber, usedLanguage = :usedLanguage WHERE userId = :userId");
                        $stmt->bindParam(':userId', $Id);
                        $stmt->bindParam(':firstName', $firstName);
                        $stmt->bindParam(':lastName', $lastName);
                        $stmt->bindParam(':phoneNumber', $phoneNumber);
                        $stmt->bindParam(':usedLanguage', $usedLanguage);

                        if ($stmt->execute()) {
                            http_response_code(200); // OK
                            echo json_encode([
                                "message" => "User updated successfully.",
                                "status" => 200,
                                "data" => [
                                    "id" => $Id,
                                    "firstName" => $firstName,
                                    "lastName" => $lastName,
                                    "phoneNumber" => $phoneNumber,
                                    "usedLanguage" => $usedLanguage
                                ],
                            ]);
                        } else {
                            http_response_code(500); // Internal Server Error
                            echo json_encode([
                                "message" => "Failed to update user in the database.",
                                "status" => 500,
                            ]);
                        }
                    } else {
                        http_response_code(404); // Not Found
                        echo json_encode([
                            "message" => "User not found.",
                            "status" => 404,
                        ]);
                    }
                }

        else {
            http_response_code(400); // Bad Request
            echo json_encode([
                "message" => "Invalid input. Both reviewCode and review fields are required.",
                "status" => 400,
            ]);
        }
    } elseif ($method === "delete") {
        $input = file_get_contents('php://input');

        if (!empty($_GET['id'])) { // Use 'Id' from input data
            $id=$_GET['id'];
            $stmt = $pdo->prepare("DELETE FROM user_product_relation WHERE userProductRelationId = :UPRI");
            $stmt->bindParam(':UPRI',   $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                http_response_code(200); // OK
                echo json_encode([
                    "message" => "Product deleted successfully.",
                    "status" => 200,
                ]);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode([
                    "message" => "Failed to delete product from the database.",
                    "status" => 500,
                ]);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode([
                "message" => "Invalid input. ID is required.",
                "status" => 400,
            ]);
        }
    } else {
        http_response_code(405); // Method Not Allowed
        echo json_encode([
            "message" => "Method not allowed.",
            "status" => 405,
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
