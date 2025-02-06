<?php
include "functions.php";
$autoload = new Functions();
$lang = $autoload->language();
$autoload->checkAutoLogin();
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
    <style>
        label, a {
            font-size: 24px;
        }

        @media (max-width: 1000px) {
            label, a {
                font-size: 48px;
            }
        }
    </style>
</head>
<body style="background: #659df7">

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

//A session segitsegevel megadjuk az adatok ertekeit
if (isset($_SESSION['email']) && isset($_SESSION['profilePic'])) {
    if (isset($_POST['searchAction'])) {
        if ($_POST['searchAction'] == 'search') {

            $usersData = new User("Guest", $_POST['searchMail'], "users.php");
            $usersData->userString();

        } else {

            $usersData = new User("Guest", 0);
            $usersData->userString();
        }
    } else {
        $sql = "SELECT * FROM user WHERE privilage != :rank";
//    if ($this->mail) {
//        $sql .= " AND userMail LIKE :mail";
//    }

        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':rank',"Admin", PDO::PARAM_STR);
//    if ($this->mail) {
//        $stmt->bindValue(':mail', '%' . $this->mail . '%', PDO::PARAM_STR);
//    }
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            $_SESSION['previousPage'] = "banSite.php";
            echo '<hr>';
            echo '<div class="container"><div class="row justify-content-around">';
            foreach ($result as $row) {
                $fullName = htmlspecialchars($row['firstName'] . ' ' . $row['lastName']);
                $isBanned = $row['banned'] == 1;

                echo '<div class="col-xl-4 p-3 border bg-dark" style="margin: auto; margin-top:100px; margin-bottom: 50px; width: fit-content;">';
                echo '<img class="profilePic" src="pictures/' . htmlspecialchars($row['profilePic']) . '" width="250" height="250" alt="Profile Picture">';
                echo '<label>ID: ' . htmlspecialchars($row['userId']) . '</label><br>';

                echo '<br><form method="post" action="functions.php">';
                echo '<input type="hidden" name="userId" value="' . htmlspecialchars($row['userId']) . '">';
                echo '<input type="hidden" name="action" value="deletePicture">';
                echo '<input type="hidden" name="table" value="user">';
                echo '<input type="submit" class="btn btn-danger" value="'.DELETE_PICTURE.'">';

                echo '</form>';
                echo '<label><b>'.NAME.':</b> ' . $fullName . '</label><br>';
                echo '<label><b>'.EMAIL.':</b><br> ' . htmlspecialchars($row['userMail']) . '</label><br>';
                echo '<label><b>'.PHONE.':</b> ' . htmlspecialchars($row['phoneNumber']) . '</label><br>';
                echo $isBanned ? '<label style="color: red;">Banned</label><br>' : '<label style="color: green;">Active</label><br>';
                echo '<form method="post" action="functions.php">';
                echo '<input type="hidden" name="userId" value="' . htmlspecialchars($row['userId']) . '">';
                echo '<input type="hidden" name="ban" value="' . ($isBanned ? 'yes' : 'no') . '">';
                echo '<input type="hidden" name="action" value="ban">';
                echo '<input type="submit" class="btn btn-danger" value="' . ($isBanned ? UNBAN_BUTTON : BAN_BUTTON) . '">';
                echo ' <a class="btn btn-primary back-button" href="modify.php?userId='.htmlspecialchars($row['userId']).'">'.MODIFY.' </a>';

                echo '</form>';
                echo '</div>';
            }
            echo '</div></div>';
        }
        echo '<hr>';
        $sql = "SELECT * FROM veterinarian";
//    if ($this->mail) {
//        $sql .= " AND userMail LIKE :mail";
//    }
        $stmt = $connection->prepare($sql);
//    if ($this->mail) {
//        $stmt->bindValue(':mail', '%' . $this->mail . '%', PDO::PARAM_STR);
//    }
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            $_SESSION['previousPage'] = "banSite.php";

            echo '<div class="container"><div class="row justify-content-around">';
            foreach ($result as $row) {
                $fullName = htmlspecialchars($row['firstName'] . ' ' . $row['lastName']);
                $isBanned = $row['banned'] == 1;

                echo '<div class="col-xl-4 p-3 border bg-dark" style="margin: auto; margin-top:100px; margin-bottom: 50px; width: fit-content;">';
                echo '<img class="profilePic" src="pictures/' . htmlspecialchars($row['profilePic']) . '" width="250" height="250" alt="Profile Picture">';
                echo '<label>ID: ' . htmlspecialchars($row['veterinarianId']) . '</label><br>';

                echo '<form method="post" action="functions.php">';
                echo '<input type="hidden" name="veterinarianId" value="' . htmlspecialchars($row['veterinarianId']) . '">';
                echo '<input type="hidden" name="action" value="deletePicture">';
                echo '<input type="hidden" name="table" value="veterinarian">';
                echo '<br><input type="submit" class="btn btn-danger" value="'.DELETE_PICTURE.'">';

                echo '</form>';

                echo '<label><b>'.NAME.':</b> ' . $fullName . '</label><br>';
                echo '<label><b>'.EMAIL.':</b><br> ' . htmlspecialchars($row['veterinarianMail']) . '</label><br>';
                echo '<label><b>'.PHONE.':</b> ' . htmlspecialchars($row['phoneNumber']) . '</label><br>';
                echo $isBanned ? '<label style="color: red;">Banned</label><br>' : '<label style="color: green;">Active</label><br>';
                echo '<form method="post" action="functions.php">';
                echo '<input type="hidden" name="veterinarianId" value="' . htmlspecialchars($row['veterinarianId']) . '">';
                echo '<input type="hidden" name="ban" value="' . ($isBanned ? 'yes' : 'no') . '">';
                echo '<input type="hidden" name="action" value="vetBan">';
                echo '<input type="submit" class="btn btn-danger" value="' . ($isBanned ? UNBAN_BUTTON : BAN_BUTTON) . '">';
                echo ' <a class="btn btn-primary back-button" href="modify.php?veterinarianId='.htmlspecialchars($row['veterinarianId']).'">'.MODIFY.' </a>';


                echo '</form>';

                echo '</div>';
            }
            echo '</div></div>';
        }
    }


} else {
    header('Location: index.php', "users.php");
    exit();
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
?>

