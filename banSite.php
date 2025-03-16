<?php
include "functions.php";
$autoload = new Functions();
$lang = $autoload->language();
$autoload->checkAutoLogin();
$pdo = $autoload->connect($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $pdoOptions);
$_SESSION['previousPage'] = "banSite.php";
?>

<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="main.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    <script src="Users.js"></script>
    <script src="search.js"></script>
    <script>
        const lang = '<?php echo $lang; ?>';

    </script>
    <style>
        label, a {
            font-size: 24px;
        }

        @media (max-width: 1000px) {
            label, a {
                font-size: 48px;
            }
        }
        .users {
            margin-top: 20px;
            padding: 15px;
            background-color: #e9f7ef;
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align: center;
            display: inline-block;
            width: 220px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .users:hover {
            transform: scale(1.05);
            background-color: #a8d5ba;
        }

        .users button {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .users button:hover {
            background-color: #218838;
        }
        #list {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start; /* Align items to the left */
        }
    </style>
</head>
<body style="background: #659df7">
<div class="d-flex flex-wrap justify-content-center">
    <div class="users">
        <form id="searchForm" method="post">
            <input type="text" id="search" name="search" placeholder="User Email" oninput="performSearch('banSite.php')">
            <input type="hidden" name="searchAction" value="1"> <!-- Add a search action field to differentiate the request -->
        </form>
    </div>
</div>
<div class="container-fluid">
    <a class="btn btn-secondary back-button" style="margin-left: 10px; margin-top: 10px" href="index.php"><?php echo BACK ?></a>
    <?php
    $_SESSION['backPic'] = "banSite.php";
    $connection = $autoload->connect($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $pdoOptions);
    if ($_SESSION['privilage'] != "Admin") {
        header("Location:index.php");
        exit();
    }
    ?>

    <?php
    if (isset($_SESSION['message']) && $_SESSION['message'] != "")
        echo "<div class='mainBlock rounded bg-dark text-white' style='text-align: center; margin-top: 100px;'>
          <h1 style='margin: auto;'>
              " . $_SESSION['message'] . "
          </h1>
          <a class='inputok' onclick='refreshPage()' style='display: inline-block; padding: 10px 20px; 
             background-color: #19451e; color: white; text-decoration: none; border-radius: 5px; 
             cursor: pointer; transition: background-color 0.3s ease; margin-top: 20px;'>
              Okay
          </a>
      </div>";
    unset($_SESSION['message']);
    ?>

</body>
</html>
<?php

if (!isset($_SESSION['email']) || !isset($_SESSION['profilePic'])) {
    header('Location: index.php');
    exit();
}
if (isset($_POST['search']) && !empty($_POST['search'])) {
    $searchTerm = "%" . $_POST['search'] . "%";

    $stmt = $pdo->prepare("SELECT * FROM user WHERE userMail LIKE :searchTerm AND privilage='User'");
    $stmt->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM veterinarian WHERE veterinarianMail LIKE :searchTerm");
    $stmt->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
    $stmt->execute();
    $veterinarians = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ob_clean(); // Clear any previous output to prevent duplication

    $usersCount = count($users);
    $vetsCount = count($veterinarians);
    $firstVetPrinted = false;
    echo "<div class='container' style='text-align: center; margin: 20px 0;'>";
    echo "<hr style='border: 2px solid white;'>";
    echo "<h3 style='color: white;'>Users</h3>";
    echo "<hr style='border: 2px solid white;'>";
    echo "</div>";
    foreach (array_merge($users, $veterinarians) as $index => $row) {
        $fullName = htmlspecialchars($row['firstName'] . ' ' . $row['lastName']);
        $isBanned = $row['banned'] == 1;

        // Insert a separator only once, before the first veterinarian
        if (!$firstVetPrinted && isset($row['veterinarianId'])) {
            echo "<div class='container' style='text-align: center; margin: 20px 0;'>";
            echo "<hr style='border: 2px solid white;'>";
            echo "<h3 style='color: white;'>Veterinarians</h3>";
            echo "<hr style='border: 2px solid white;'>";
            echo "</div>";
            $firstVetPrinted = true; // Ensure it only prints once
        }

        echo "<div class='col-xl-4 p-3 border bg-dark' style='margin: auto; margin-top:100px; margin-bottom: 50px; width: fit-content;'>";
        echo "<img class='profilePic' src='pictures/" . htmlspecialchars($row['profilePic']) . "' width='250' height='250' alt='Profile Picture'>";

        echo '<br><form method="post" action="functions.php">';

        if (isset($row['userId'])) {
            echo '<input type="hidden" name="userId" value="' . htmlspecialchars($row['userId']) . '">';
        } else {
            echo '<input type="hidden" name="userId" value="' . htmlspecialchars($row['veterinarianId']) . '">';
        }

        echo '<input type="hidden" name="action" value="deletePicture">';
        echo '<input type="hidden" name="table" value="user">';
        echo '<input type="submit" class="btn btn-danger" value="' . DELETE_PICTURE . '"></form>';

        echo "<label>ID: " . htmlspecialchars($row['userId'] ?? $row['veterinarianId']) . "</label><br>";
        echo "<label><b>Név:</b> " . htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) . "</label><br>";
        echo "<label><b>Email:</b> " . htmlspecialchars($row['userMail'] ?? $row['veterinarianMail']) . "</label><br>";
        echo "<label><b>Telefon:</b> " . htmlspecialchars($row['phoneNumber']) . "</label><br>";
        echo "<label style='color: " . ($row['banned'] == 1 ? 'red' : 'green') . ";'>";
        echo ($row['banned'] == 1 ? 'Banned' : 'Active') . "</label><br>";

        echo '<form method="post" action="functions.php">';

        if (isset($row['userId'])) {
            echo '<input type="hidden" name="userId" value="' . htmlspecialchars($row['userId']) . '">';
            echo '<input type="hidden" name="action" value="ban">';
        } else {
            echo '<input type="hidden" name="veterinarianId" value="' . htmlspecialchars($row['veterinarianId']) . '">';
            echo '<input type="hidden" name="action" value="vetBan">';
        }

        echo '<input type="hidden" name="ban" value="' . ($isBanned ? 'yes' : 'no') . '">';
        echo '<input type="submit" class="btn btn-danger" value="' . ($isBanned ? UNBAN_BUTTON : BAN_BUTTON) . '">';

        if (isset($row['userId'])) {
            echo ' <a class="btn btn-primary back-button" href="modify.php?userId=' . htmlspecialchars($row['userId']) . '">' . MODIFY . ' </a>';
        } else {
            echo ' <a class="btn btn-primary back-button" href="modify.php?veterinarianId=' . htmlspecialchars($row['veterinarianId']) . '">' . MODIFY . ' </a>';
        }

        echo '</form>';
        echo "</div>";
    }


    exit(); // Stop further execution
}
else {
    $stmt = $pdo->prepare("SELECT * FROM user WHERE privilage='User'");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->prepare("SELECT * FROM veterinarian");
    $stmt->execute();
    $veterinarians = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<div id="list" class="d-flex flex-wrap justify-content-center">';
    $usersCount = count($users);
    $vetsCount = count($veterinarians);
    $firstVetPrinted = false;
    echo "<div class='container' style='text-align: center; margin: 20px 0;'>";
    echo "<hr style='border: 2px solid white;'>";
    echo "<h3 style='color: white;'>Users</h3>";
    echo "<hr style='border: 2px solid white;'>";
    echo "</div>";
    foreach (array_merge($users, $veterinarians) as $index => $row) {
        $fullName = htmlspecialchars($row['firstName'] . ' ' . $row['lastName']);
        $isBanned = $row['banned'] == 1;

        // Insert a separator only once, before the first veterinarian
        if (!$firstVetPrinted && isset($row['veterinarianId'])) {
            echo "<div class='container' style='text-align: center; margin: 20px 0;'>";
            echo "<hr style='border: 2px solid white;'>";
            echo "<h3 style='color: white;'>Veterinarians</h3>";
            echo "<hr style='border: 2px solid white;'>";
            echo "</div>";
            $firstVetPrinted = true; // Ensure it only prints once
        }

        echo "<div class='col-xl-4 p-3 border bg-dark' style='margin: auto; margin-top:100px; margin-bottom: 50px; width: fit-content;'>";
        echo "<img class='profilePic' src='pictures/" . htmlspecialchars($row['profilePic']) . "' width='250' height='250' alt='Profile Picture'>";

        echo '<br><form method="post" action="functions.php">';

        if (isset($row['userId'])) {
            echo '<input type="hidden" name="userId" value="' . htmlspecialchars($row['userId']) . '">';
        } else {
            echo '<input type="hidden" name="userId" value="' . htmlspecialchars($row['veterinarianId']) . '">';
        }

        echo '<input type="hidden" name="action" value="deletePicture">';
        echo '<input type="hidden" name="table" value="user">';
        echo '<input type="submit" class="btn btn-danger" value="' . DELETE_PICTURE . '"></form>';

        echo "<label>ID: " . htmlspecialchars($row['userId'] ?? $row['veterinarianId']) . "</label><br>";
        echo "<label><b>Név:</b> " . htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) . "</label><br>";
        echo "<label><b>Email:</b> " . htmlspecialchars($row['userMail'] ?? $row['veterinarianMail']) . "</label><br>";
        echo "<label><b>Telefon:</b> " . htmlspecialchars($row['phoneNumber']) . "</label><br>";
        echo "<label style='color: " . ($row['banned'] == 1 ? 'red' : 'green') . ";'>";
        echo ($row['banned'] == 1 ? 'Banned' : 'Active') . "</label><br>";

        echo '<form method="post" action="functions.php">';

        if (isset($row['userId'])) {
            echo '<input type="hidden" name="userId" value="' . htmlspecialchars($row['userId']) . '">';
            echo '<input type="hidden" name="action" value="ban">';
        } else {
            echo '<input type="hidden" name="veterinarianId" value="' . htmlspecialchars($row['veterinarianId']) . '">';
            echo '<input type="hidden" name="action" value="vetBan">';
        }

        echo '<input type="hidden" name="ban" value="' . ($isBanned ? 'yes' : 'no') . '">';
        echo '<input type="submit" class="btn btn-danger" value="' . ($isBanned ? UNBAN_BUTTON : BAN_BUTTON) . '">';

        if (isset($row['userId'])) {
            echo ' <a class="btn btn-primary back-button" href="modify.php?userId=' . htmlspecialchars($row['userId']) . '">' . MODIFY . ' </a>';
        } else {
            echo ' <a class="btn btn-primary back-button" href="modify.php?veterinarianId=' . htmlspecialchars($row['veterinarianId']) . '">' . MODIFY . ' </a>';
        }

        echo '</form>';
        echo "</div>";
    }

}


?>

<script>
function banUser(name) {
    if (confirm("Are you sure about banning " + name + "?")) {
        document.getElementById("banForm").submit();
    } else {
        console.log("Ban action canceled.");
    }
}
</script>


