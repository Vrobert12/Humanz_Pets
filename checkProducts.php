<?php

include "functions.php";
$autoload = new Functions();
$lang = $autoload->language();
$autoload->checkAutoLogin();

$pdo = $autoload->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);
if($_SESSION['privilage']!='Admin'){
    header('Location:index.php');
    exit();
}

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
    <script src="indexJS.js"></script>
    <script src="search.js"></script>
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
    </style>
</head>
<body style="background: #659df7">
<div class="d-flex flex-wrap justify-content-center">
    <div class="users">
        <form id="searchForm" method="post">
            <input type="text" id="search" name="search" placeholder="User Email" oninput="performSearch('checkProducts.php')">
            <input type="hidden" name="searchAction" value="1"> <!-- Add a search action field to differentiate the request -->
        </form>
    </div>
</div>
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

<a class="btn btn-secondary back-button" style="margin-left: 10px; margin-top: 10px" href="index.php"><?php echo BACK ?></a>
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

if(isset($_SESSION['email']) && isset($_SESSION['name']) && isset($_SESSION['profilePic']) && $_SESSION['privilage']=='Admin'){
    $_SESSION['backPic'] = "checkProducts.php";

    if (!empty($_POST['search'])) {
        $params = "%" . $_POST['search'] . "%";
        $stmt = "SELECT p.userId, u.*, GROUP_CONCAT(p.userProductRelationId) AS userProductRelationIds, 
             GROUP_CONCAT(p.productName) AS productNames
             FROM user u
             INNER JOIN user_product_relation p ON p.userId = u.userId
             WHERE u.verify = 1
             AND u.banned != 1
             AND p.productPayed = 0
             AND userMail LIKE :email
             GROUP BY u.userId
             ORDER BY u.userId ASC";
        try {
            // Prepare the SQL query
            $stmt = $pdo->prepare($stmt);
            $stmt->execute(array('email' => $params)); // Execute the query with the search parameter

            // Fetch all results
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo '<div id="list" class="d-flex flex-wrap justify-content-center">';
            if (!empty($results)) {
                echo '<div class="container">
                <div class="row justify-content-around">';
                foreach ($results as $row) {
                    echo '<div class="col-xl-4 p-5 border bg-dark" style="margin: auto; margin-top:100px; margin-bottom: 50px; width: fit-content">';
                    echo '<div class="col-xl-4"><img class="profilePic" 
                src="pictures/' . htmlspecialchars($row['profilePic']) . '" width="250" height="250" alt="Profile Picture"></div>';
                    echo '<label>ID: ' . htmlspecialchars($row['userId']) . '</label><br>';
                    echo '<label>' . NAME . ': ' . htmlspecialchars($row['firstName'] . " " . $row['lastName']) . '</label><br>';
                    echo '<label>' . PHONE . ': ' . htmlspecialchars($row['phoneNumber']) . '</label><br>';
                    echo '<label>' . EMAIL . ': ' . htmlspecialchars($row['userMail']) . '</label><br>';
                    echo '<a class="btn btn-primary" href="usersProducts.php?user=' . htmlspecialchars($row['userId']) . '">' . RESERVE . '</a>&nbsp;&nbsp;&nbsp;';
                    echo '</div>';
                }
                echo '</div></div>';
            } else {
                $_SESSION['message'] = "<h2 style='color: white'>No result found.</h2>";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        $stmt = $pdo->prepare("SELECT p.userId, u.*, GROUP_CONCAT(p.userProductRelationId) AS userProductRelationIds, 
                           GROUP_CONCAT(p.productName) AS productNames
                           FROM user u
                           INNER JOIN user_product_relation p ON p.userId = u.userId
                           WHERE u.verify = 1
                           AND u.banned != 1
                           AND p.productPayed = 0
                           GROUP BY u.userId
                           ORDER BY u.userId ASC");
        try {
            // Prepare the SQL query and execute it
            $stmt->execute();

            // Fetch all results
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo '<div id="list" class="d-flex flex-wrap justify-content-center">';
            if (!empty($results)) {
                echo '<div class="container">
                <div class="row justify-content-around">';
                foreach ($results as $row) {
                    echo '<div class="col-xl-4 p-5 border bg-dark" style="margin: auto; margin-top:100px; margin-bottom: 50px; width: fit-content">';
                    echo '<div class="col-xl-4"><img class="profilePic" 
                src="pictures/' . htmlspecialchars($row['profilePic']) . '" width="250" height="250" alt="Profile Picture"></div>';
                    echo '<label>ID: ' . htmlspecialchars($row['userId']) . '</label><br>';
                    echo '<label>' . NAME . ': ' . htmlspecialchars($row['firstName'] . " " . $row['lastName']) . '</label><br>';
                    echo '<label>' . PHONE . ': ' . htmlspecialchars($row['phoneNumber']) . '</label><br>';
                    echo '<label>' . EMAIL . ': ' . htmlspecialchars($row['userMail']) . '</label><br>';
                    echo '<a class="btn btn-primary" href="usersProducts.php?user=' . htmlspecialchars($row['userId']) . '">' . RESERVE . '</a>&nbsp;&nbsp;&nbsp;';
                    echo '</div>';
                }
                echo '</div></div></div>';
            } else {
                $_SESSION['message'] = "<h2 style='color: white'>No result found.</h2>";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

}

else {
    // Redirect to index.php if session variables are not set
    header('Location: index.php');
    exit();
}

?>
</body>
</html>
