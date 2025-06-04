<?php

include "functions.php";
$autoload = new Functions();
$lang = $autoload->language();
$autoload->checkAutoLogin();

$pdo = $autoload->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);
if ($_SESSION['privilage'] != "Admin") {
    header("Location:index.php");
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
<body style="background: #659df7">

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

<a class="btn btn-secondary back-button" style="margin-left: 10px; margin-top: 10px" href="index.php"><?php echo BACK ?></a>
<div class="d-flex justify-content-center" style="margin-top: 20px; margin-bottom: 30px;">
    <form id="searchForm" method="get" action="veterinarianRates.php" class="d-flex" style="gap:10px; align-items:center;">
        <input type="text" id="search" name="search" placeholder="<?php echo EMAIL;?>"
               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
               class="form-control" style="max-width: 250px; font-size: 14px;">
        <button type="submit" class="btn btn-primary" style="font-size: 14px; padding: 6px 12px;">Keresés</button>
    </form>
</div>


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

if (isset($_SESSION['email']) && isset($_SESSION['name']) && isset($_SESSION['profilePic'])) {

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $searchTerm = "%" . trim($_GET['search']) . "%";

        // Keresés a user táblában
        $stmt = $pdo->prepare("SELECT * FROM veterinarian WHERE veterinarianMail LIKE :searchTerm");
        $stmt->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Keresés az állatorvosok között
        $stmt = $pdo->prepare("SELECT v.veterinarianId,v.firstName,v.lastName,v.veterinarianMail,v.profilePic,v.phoneNumber,v.banned,
        COALESCE(AVG(r.review), 0) AS totalReviewSum FROM veterinarian v 
        LEFT JOIN review r ON v.veterinarianId = r.veterinarianId
        WHERE v.verify=1 AND v.veterinarianMail LIKE :searchTerm 
        GROUP BY v.veterinarianId");
        $stmt->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        $veterinarians = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Display Veterinarians
        if (!empty($veterinarians)) {
            echo '<div class="container">
                    <div class="row justify-content-around">';
            foreach ($veterinarians as $row) {
                $isBanned = $row['banned'] == 1;
                echo '<div class="col-xl-4 p-5 border bg-dark" style="margin: auto; margin-top:00px; margin-bottom: 50px; width: fit-content">';
                echo '<div class="col-xl-4"><img class="profilePic" 
                src="pictures/' . htmlspecialchars($row['profilePic']) . '" width="250" height="250" alt="Profile Picture"></div>';
                echo '<label>ID: ' . htmlspecialchars($row['veterinarianId']) . '</label><br>';
                echo '<label>Name: ' . htmlspecialchars($row['firstName'] . " " . $row['lastName']) . '</label><br>';
                echo '<label>Phone: ' . htmlspecialchars($row['phoneNumber']) . '</label><br>';
                echo '<label>Email: ' . htmlspecialchars($row['veterinarianMail']) . '</label><br>';
                echo $isBanned ? '<label style="color: red;">Banned</label><br>' : '<label style="color: green;">Active</label><br>';

                $_SESSION['previousPage']="veterinarianRates.php";
                echo '<label><a class="btn btn-primary" href="ratings.php">Review: <b>5 / ' . htmlspecialchars((float)$row['totalReviewSum']) . '</b></a></label><br><br>';
                echo '<a class="btn btn-primary" href="book_apointment.php?email=' . $_SESSION['email'] . '&veterinarian=' . htmlspecialchars($row['veterinarianId']) . '">Reserve</a>&nbsp;&nbsp;&nbsp;';
                echo '</div>';
            }
            echo '</div></div>';
        } else {
            $_SESSION['message'] = "<h2 style='color: white'>No result found.</h2>";
        }
    }
    else {
        $stmt = $pdo->prepare("SELECT v.veterinarianId,v.firstName,v.lastName,v.veterinarianMail,v.profilePic,v.phoneNumber,v.banned,
        COALESCE(AVG(r.review), 0) AS totalReviewSum FROM veterinarian v 
        LEFT JOIN review r ON v.veterinarianId = r.veterinarianId
        WHERE v.verify=1
        GROUP BY v.veterinarianId");
        $stmt->execute();
        $veterinarians = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Display Veterinarians
        if (!empty($veterinarians)) {
            echo '<div id="list" class="d-flex flex-wrap justify-content-center">';
            echo '<div class="container">
                    <div class="row justify-content-around">';
            foreach ($veterinarians as $row) {
                $isBanned = $row['banned'] == 1;
                echo '<div class="col-xl-4 p-5 border bg-dark" style="margin: auto; margin-top:0px; margin-bottom: 50px; width: fit-content">';
                echo '<div class="col-xl-4"><img class="profilePic" 
                src="pictures/' . htmlspecialchars($row['profilePic']) . '" width="250" height="250" alt="Profile Picture"></div>';
                echo '<label>ID: ' . htmlspecialchars($row['veterinarianId']) . '</label><br>';
                echo '<label>Name: ' . htmlspecialchars($row['firstName'] . " " . $row['lastName']) . '</label><br>';
                echo '<label>Phone: ' . htmlspecialchars($row['phoneNumber']) . '</label><br>';
                echo '<label>Email: ' . htmlspecialchars($row['veterinarianMail']) . '</label><br>';
                echo $isBanned ? '<label style="color: red;">Banned</label><br>' : '<label style="color: green;">Active</label><br>';

                $_SESSION['previousPage']="veterinarianRates.php";
                echo '<label><a class="btn btn-primary" href="ratings.php">Review: <b>5 / ' . htmlspecialchars((float)$row['totalReviewSum']) . '</b></a></label><br><br>';
                echo '<a class="btn btn-primary" href="book_apointment.php?email=' . $_SESSION['email'] . '&veterinarian=' . htmlspecialchars($row['veterinarianId']) . '">Reserve</a>&nbsp;&nbsp;&nbsp;';
                echo '</div>';
            }
            echo '</div></div>';
        } else {
            $_SESSION['message'] = "<h2 style='color: white'>No result found.</h2>";
        }
    }
}
?>
</body>
</html>
