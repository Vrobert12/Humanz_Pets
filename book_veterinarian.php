<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    $lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
    if(isset($_GET['lang'])){
        $_SESSION['lang'] = $_GET['lang'];
    }
    include "lang_$lang.php";
}

include "functions.php";
$autoload=new Functions();
$autoload->checkAutoLogin();

$pdo = $autoload->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);


?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Main Page</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=yes">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
              integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
                crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
        <script>
            const lang = '<?php echo $lang; ?>';
        </script>
        <script src="LogOut.js"></script>
        <script src="indexJS.js"></script>
        <link rel="stylesheet" href="style.css">
       <style>


           th {
               background-color: lightblue;
           }

           label {
               color: white;
           }

           td, th {
               padding: 15px;
               font-size: 20px;
           }

       </style>
    </head>
    <body>

    <!-- Show popup message if session message is set -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="popup-message" id="popupMessage">
            <?php echo $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message']); // Clear message after it's displayed ?>
    <?php endif; ?>

    <script>

    </script>

    <!-- Show popup message if session message is set -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="popup-message" id="popupMessage">
            <?php echo $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message']); // Clear message after it's displayed ?>
    <?php endif; ?>

    <script>
        // Show the popup message and hide it after 5 seconds
        window.onload = function () {
            var popupMessage = document.getElementById('popupMessage');
            if (popupMessage) {
                popupMessage.style.display = 'block';  // Show the popup

                // Hide the popup after 5 seconds
                setTimeout(function () {
                    popupMessage.style.display = 'none';
                }, 5000);
            }
        };
    </script>
    <!--
    https://getbootstrap.com/docs/5.3/components/navbar/
    -->
    <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand" href="#">Logo</a>
            <!-- Toggler for mobile view -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Navbar Content -->
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
                <!-- Centered Navigation Links -->
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="contacts.php"></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"></a>
                    </li>
                    <li class="nav-item">
                        <?php
                        $sql = "SELECT * FROM user";
                        ?>
                        <a class="nav-link" href="book_veterinarian.php"><i class="bi bi-book-fill fs-3"></i></a>
                    </li>
                </ul>
                <!-- Language Dropdown (Aligned Right) -->
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo LG ?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="?lang=en"><?php echo LANGUAGE_en ?></a>
                        <a class="dropdown-item" href="?lang=hu"><?php echo LANGUAGE_hu ?></a>
                        <a class="dropdown-item" href="?lang=sr"><?php echo LANGUAGE_sr ?></a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
<?php
if(isset($_SESSION['message']) && $_SESSION['message'] != "")
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


$_SESSION['message'] = "";
?>

</body>
</html>
<?php
if (isset($_SESSION['email']) && isset($_SESSION['name']) && isset($_SESSION['profilePic'])) {
    // Set a session variable for returning to tables.php
    $_SESSION['backPic'] = "book_veterinarian.php";

    if (isset($_POST['searchAction']) && $_POST['searchAction'] == 'search') {
        $veterinarianLike = "%" . $_POST['searchName'] . "%";
        users("SELECT * FROM veterinarian WHERE area LIKE ?", [$veterinarianLike]);
    } else {
        users("SELECT * FROM veterinarian");
    }
} else {
    // Redirect to index.php if session variables are not set
    header('Location: index.php');
    exit();
}

function users($command, $params = [])
{
    global $pdo;

    $_SESSION['reservation'] = 0;

    try {
        // Prepare the SQL query
        $stmt = $pdo->prepare($command);

        // Bind parameters dynamically
        foreach ($params as $index => $value) {
            $stmt->bindValue($index + 1, $value, PDO::PARAM_STR); // Positional parameters start at index 1
        }

        // Execute the query
        $stmt->execute();

        // Fetch all results
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($results)) {
            echo '<div class="container">
                    <div class="row justify-content-around">';
            foreach ($results as $row) {
                echo '<div class="col-xl-4 p-5 border bg-dark" style="margin: auto; margin-top:100px; margin-bottom: 50px; width: fit-content">';
                echo '<div class="col-xl-4"><img class="profilePic" 
                src="pictures/' . htmlspecialchars($row['profilePic']) . '" width="250" height="250" alt="Profile Picture"></div>';
                echo '<label>ID: ' . htmlspecialchars($row['veterinarianId']) . '</label><br>';
                echo '<label>Name: ' . htmlspecialchars($row['firstName'] . " " . $row['lastName']) . '</label><br>';
                echo '<label>Phone: ' . htmlspecialchars($row['phoneNumber']) . '</label><br>';
                echo '<label>Email: ' . htmlspecialchars($row['veterinarianMail']) . '</label><br>';
                echo '<a href="book_apointment.php?veterinarian=' . htmlspecialchars($row['veterinarianId']) . '">Reserve</a>&nbsp;&nbsp;&nbsp;';
                echo '</div>';
            }
            echo '</div></div>';
        } else {
            $_SESSION['message'] = "<h2 style='color: white'>No result found.</h2>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
