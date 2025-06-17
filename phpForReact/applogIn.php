<?php
global $pdo;
require_once 'react_config.php';
ob_clean();
header("Content-Type: application/json");

// Read incoming JSON data
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($data["userId"]) && isset($data["token"])) {
        // User session validation
        $userId = $data["userId"];
        $token = $data["token"];

        $stmt = $pdo->prepare("SELECT session_token, banned FROM user WHERE userId = :userId");
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $banned = $user['banned'];

        if ($user && $user["session_token"] === $token && $banned !== 1) {
            // Get language as well
            $userLangStmt = $pdo->prepare("SELECT usedLanguage, banned FROM user WHERE userId = :userId");
            $userLangStmt->bindParam(":userId", $userId, PDO::PARAM_INT);
            $userLangStmt->execute();
            $langResult = $userLangStmt->fetch(PDO::FETCH_ASSOC);
            $lang = $langResult ? (string)$langResult["usedLanguage"] : "en"; // default to 'en' if null

            // Fetch updated pet data
            $petStmt = $pdo->prepare("SELECT petId, petName, veterinarId, bred, petSpecies, profilePic FROM pet WHERE userId = :userId");
            $petStmt->bindParam(":userId", $userId, PDO::PARAM_INT);
            $petStmt->execute();
            $pets = $petStmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "message" => "Session valid",
                "userid" => $userId,
                "pets" => $pets,
                "language" => $lang,
                "banned" => $banned
            ]);
            exit();
        }
    }

    // Normal login process
    $email = $data["email"] ?? "";
    $password = $data["password"] ?? "";

    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Email and password required"]);
        exit();
    }

    $stmt = $pdo->prepare("SELECT userId, userPassword, usedLanguage, banned FROM user WHERE userMail = :email");
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $banned2 = $user['banned'];

    if ($user) {
        if (password_verify($password, $user["userPassword"])) {
            if ((string)$user['banned'] === "1") {
                echo json_encode(["success" => false, "message" => "banned"]);
                exit();
            }

            $userId = $user["userId"];
            $lang = (string)$user["usedLanguage"];
            $token = bin2hex(random_bytes(32));

            // Store the token in DB
            $updateStmt = $pdo->prepare("UPDATE user SET session_token = :token WHERE userId = :id");
            $updateStmt->bindParam(":token", $token, PDO::PARAM_STR);
            $updateStmt->bindParam(":id", $userId, PDO::PARAM_INT);
            $updateStmt->execute();

            // Fetch pet data
            $petStmt = $pdo->prepare("SELECT petId, petName, veterinarId, bred, petSpecies, profilePic FROM pet WHERE userId = :id");
            $petStmt->bindParam(":id", $userId, PDO::PARAM_INT);
            $petStmt->execute();
            $pets = $petStmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["success" => true, "message" => "Login successful", "userid" => $userId, "token" => $token, "pets" => $pets, "language" => $lang, "banned" => $banned2]);
            exit();
        } else {
            echo json_encode(["success" => false, "message" => "Invalid credentials"]);
            exit();
        }
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
