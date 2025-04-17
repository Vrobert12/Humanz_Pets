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

// Read incoming JSON data
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($data["userId"]) && isset($data["token"])) {
        // User session validation
        $userId = $data["userId"];
        $token = $data["token"];

        $stmt = $conn->prepare("SELECT session_token FROM user WHERE userId = :userId");
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user["session_token"] === $token) {
            // Get language as well
            $userLangStmt = $conn->prepare("SELECT usedLanguage FROM user WHERE userId = :userId");
            $userLangStmt->bindParam(":userId", $userId, PDO::PARAM_INT);
            $userLangStmt->execute();
            $langResult = $userLangStmt->fetch(PDO::FETCH_ASSOC);
            $lang = $langResult ? (string)$langResult["usedLanguage"] : "en"; // default to 'en' if null

            // Fetch updated pet data
            $petStmt = $conn->prepare("SELECT petId, petName, veterinarId, bred, petSpecies, profilePic FROM pet WHERE userId = :userId");
            $petStmt->bindParam(":userId", $userId, PDO::PARAM_INT);
            $petStmt->execute();
            $pets = $petStmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "message" => "Session valid",
                "userid" => $userId,
                "pets" => $pets,
                "language" => $lang
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

    $stmt = $conn->prepare("SELECT userId, userPassword, usedLanguage FROM user WHERE userMail = :email");
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user["userPassword"])) {
            $userId = $user["userId"];
            $lang = (string)$user["usedLanguage"];
            $token = bin2hex(random_bytes(32)); // Generate a new session token

            // Store the token in the database
            $updateStmt = $conn->prepare("UPDATE user SET session_token = :token WHERE userId = :id");
            $updateStmt->bindParam(":token", $token, PDO::PARAM_STR);
            $updateStmt->bindParam(":id", $userId, PDO::PARAM_INT);
            $updateStmt->execute();

            // Fetch pet data
            $petStmt = $conn->prepare("SELECT petId, petName, veterinarId, bred, petSpecies, profilePic FROM pet WHERE userId = :id");
            $petStmt->bindParam(":id", $userId, PDO::PARAM_INT);
            $petStmt->execute();
            $pets = $petStmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(["success" => true, "message" => "Login successful", "userid" => $userId, "token" => $token, "pets" => $pets, "language" => $lang]);
            exit();
        } else {
            echo json_encode(["success" => false, "message" => "Invalid credentials"]);
            exit();
        }
    } else {
        echo json_encode(["success" => false, "message" => "User not found"]);
        exit();
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
