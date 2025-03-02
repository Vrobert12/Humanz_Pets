<?php
header("Content-Type: application/json");

$host = "localhost";
$dbname = "pets";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["success" => false, "message" => "Database connection failed"]));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data["email"] ?? "";
    $password = $data["password"] ?? "";

    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Email and password required"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT userId, userPassword FROM user WHERE userMail = :email");
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $userId = $user["userId"];

    if ($user) {
        if (password_verify($password, $user["userPassword"])) {
            // Generate a random session token
            $token = bin2hex(random_bytes(32));

            // Store the token in the database
            $updateStmt = $conn->prepare("UPDATE user SET session_token = :token WHERE userId = :id");
            $updateStmt->bindParam(":token", $token, PDO::PARAM_STR);
            $updateStmt->bindParam(":id", $user["userId"], PDO::PARAM_INT);
            $updateStmt->execute();

            echo json_encode(["success" => true, "message" => "Login successful", "userid" => $userId, "token" => $token]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid credentials"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "User not found"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
