<?php

header("Content-Type: application/json");

$host = "localhost";
$dbname = "pets";
$username = "root";
$password = "";



try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Read JSON input
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if all fields are provided
    if (!isset($data['firstname'], $data['lastname'], $data['phone'], $data['email'], $data['language'], $data['password'])) {
        echo json_encode(["success" => false, "message" => "Missing required fields"]);
        exit();
    }

    // Trim and sanitize inputs
    $firstname = htmlspecialchars(trim($data['firstname']));
    $lastname = htmlspecialchars(trim($data['lastname']));
    $phone = htmlspecialchars(trim($data['phone']));
    $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
    $language = trim($data['language']);
    if($language == 'English'){
        $language = 'en';
    }
    elseif ($language == 'Hungarian'){
        $language = 'hu';
    }
    else{$language = 'sr';}
    $password = password_hash(trim($data['password']), PASSWORD_BCRYPT);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Invalid email format"]);
        exit();
    }

    // Check if the email already exists
    $checkQuery = "SELECT userId FROM user WHERE userMail = :email";
    $stmt = $pdo->prepare($checkQuery);
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => false, "message" => "Email already in use"]);
        exit();
    }

    // Insert new user
    $insertQuery = "INSERT INTO user (firstname, lastname, phoneNumber, userMail, usedLanguage, userPassword) 
                    VALUES (:firstname, :lastname, :phone, :email, :language, :password)";
    $stmt = $pdo->prepare($insertQuery);

    $result = $stmt->execute([
        'firstname' => $firstname,
        'lastname'  => $lastname,
        'phone'     => $phone,
        'email'     => $email,
        'language'  => $language,
        'password'  => $password
    ]);

    if ($result) {
        echo json_encode(["success" => true, "message" => "User registered successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
