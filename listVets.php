<?php

include "functions.php";
$autoload = new Functions();
$lang = $autoload->language();
include "lang_$lang.php";
$autoload->checkAutoLogin();

$pdo = $autoload->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);

$backgroundImage = "pictures/background.jpg";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Main Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script>
        const lang = '<?php echo $lang; ?>';
    </script>
    <script src="LogOut.js"></script>
    <script src="indexJS.js"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: #659df7;
        }
        .popup-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 15px;
            border-radius: 5px;
            display: none;
            z-index: 1000;
        }
        .list-group-item {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            text-align: center;
            justify-content: center;
        }
        .list-group-item img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>

<?php if (isset($_SESSION['message'])): ?>
    <div class="popup-message" id="popupMessage">
        <?php echo $_SESSION['message']; ?>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<script>
    window.onload = function () {
        var popupMessage = document.getElementById('popupMessage');
        if (popupMessage) {
            popupMessage.style.display = 'block';
            setTimeout(function () {
                popupMessage.style.display = 'none';
            }, 5000);
        }
    };
</script>

<div class="container mt-3">
    <a class="btn btn-secondary" href="index.php"><?php echo BACK; ?></a>
</div>

<?php
$_SESSION['backPic'] = "book_veterinarian.php";

if (isset($_POST['searchAction']) && $_POST['searchAction'] == 'search') {
    $veterinarianLike = "%" . $_POST['searchName'] . "%";
    users("SELECT * FROM veterinarian WHERE verify=1 and banned!=1 and area LIKE ?", [$veterinarianLike]);
} else {
    users("SELECT * FROM veterinarian WHERE verify=1 and banned!=1");
}

function users($command, $params = [])
{
    global $pdo;
    $_SESSION['reservation'] = 0;

    try {
        $stmt = $pdo->prepare($command);
        foreach ($params as $index => $value) {
            $stmt->bindValue($index + 1, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($results)) {
            echo '<div class="container my-3">
                  <div class="row">';
            foreach ($results as $row) {
                echo '<div class="col-md-6 col-lg-4 mb-3">
                        <div class="card p-3 text-center">
                            <img src="pictures/' . htmlspecialchars($row['profilePic']) . '" class="rounded-circle img-fluid mx-auto" width="80" height="80" alt="Profile Picture">
                            <h5 class="mt-2">' . htmlspecialchars($row['firstName'] . " " . $row['lastName']) . '</h5>
                            <p><strong>ID:</strong> ' . htmlspecialchars($row['veterinarianId']) . '</p>
                            <p><strong>'.PHONE.':</strong> ' . htmlspecialchars($row['phoneNumber']) . '</p>
                            <p><strong>'.EMAIL.':</strong> ' . htmlspecialchars($row['veterinarianMail']) . '</p>
                        </div>
                      </div>';
            }
            echo '</div></div>';
        } else {
            $_SESSION['message'] = "<h2 style='color: white'>No result found.</h2>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
</body>
</html>
