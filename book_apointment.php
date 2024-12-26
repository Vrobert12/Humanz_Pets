<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <script>
        function activateSubmit() {
            // Activate the submit button when a file is selected
            document.getElementById('submitButton').click();
        }

        function activateSubmit2() {
            // Activate the submit button when a file is selected
            document.getElementById('submit2').click();
        }

        function logoutAndRedirect() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'functions.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    // Redirect to MainPage.php after successful logout
                    window.location.href = 'reservation.php';
                } else {
                    // Handle logout error
                    console.error('Logout failed with status ' + xhr.status);
                }
            };
            xhr.send();
        }

        function fetchDishesByType() {
            var selectedType = document.getElementById("dishTypeSelect").value;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_dishes.php?dishType=" + selectedType, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById("dishContainer").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        function refreshPage() {
            window.location.reload();
        }
    </script>
    <!--    <script src="dishes.js"></script>-->
</head>
<body>
<?php
include 'functions.php';
$functions = new Functions();
$pdo = $functions->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);

$sqlTable = $pdo->prepare("SELECT MAX(veterinarianId) AS max_veterinarianId FROM veterinarian");
$sqlTable->execute();

// Fetch the result
$result = $sqlTable->fetch(PDO::FETCH_ASSOC);

if ($result && $sqlTable->rowCount() > 0) {
    $_SESSION['maxVeterinarianId'] = $result['max_veterinarianId'];
} else {
    $_SESSION['maxVeterinarianId'] = null; // Handle the case where no rows are returned
}


$today = date("Y-m-d");
$todayHour = date("H:i:s");

if (isset($_POST['delete']) && $_POST['delete'] = 'delete') {
    $sql = mysqli_prepare($pdo, "SELECT * FROM `veterinarian` WHERE veterinarianId=?");
    $sql->bind_param('i', $_POST['reservation']);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if (strtotime($row['reservationTime']) > strtotime($todayHour) + 4 * 60 * 60) {

                $sqlDelete = mysqli_prepare($conn, "delete  FROM reservation where reservationId = ? ");
                $sqlDelete->bind_param('i', $_POST['reservation']);
                $sqlDelete->execute();
                $resultDelete = $sqlDelete->get_result();

            } else {
                if ($row['reservationDay'] != $today ) {

                    $sqlDelete = mysqli_prepare($conn, "delete  FROM reservation where reservationId = ? ");
                    $sqlDelete->bind_param('i', $_POST['reservation']);
                    $sqlDelete->execute();
                    $resultDelete = $sqlDelete->get_result();
                }
                else{
                    $_SESSION['reservationMessage'] = "You can not resign the table " . $_GET['table'] . ", 4 hours before the reservation!";
                }

            }
        }
    }


}
$sqlVeterinarian = $pdo->prepare("SELECT max(veterinarianId) AS max_veterinarianId FROM veterinarian");
$sqlVeterinarian ->execute();
$result=$sqlVeterinarian->fetch();

if ($result>0) {
    $row = $result;
    $_SESSION['max_veterinarianId'] = $row['max_veterinarianId'];
}

?>
<form method="post"
      action="book_apointment.php?veterinarian=<?php if (isset($_GET['veterinarian']) && $_GET['veterinarian'] > 0 && $_GET['veterinarian'] <= $_SESSION['maxVeterinarianId']) echo $_GET['veterinarian']; else {
          header('location:book_veterinarian.php');
          exit();
      }?>" class="reservationForm">
<form class="menuForm" method="post">
    <h2>Menu</h2>
    <label class="bold">Type:
        <select class="inputok" id="dishTypeSelect" onchange="fetchDishesByType()">
            <?php
            $sqlType = $pdo->prepare("SELECT DISTINCT firstName, lastname FROM veterinarian");
            $sqlType->execute();
            $res = $sqlType->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows as an associative array

            if (!empty($res)) {
                foreach ($res as $row) {
                    echo "<option value='" . htmlspecialchars($row['firstName'] . " " . $row['lastname']) . "'>"
                        . htmlspecialchars($row['firstName'] . " " . $row['lastname']) . "</option>";
                }
            } else {
                echo "<option value=''>No veterinarians available</option>";
            }
            ?>
        </select>
    </label>
    <br><br>
    <label for="codeInput">Coupon code:</label>
    <input type="text" name="code" id="codeInput"><br>
    <input class="inputok" type="submit" name="action" value="UseCoupon">
    <div class="container text-center" id="dishContainer">
        <?php

        ob_start(); // Start output buffering

        if (isset($_POST['action']) && $_POST['action'] == 'UseCoupon') {
            $_SESSION['couponDiscount'] = 1;
            // Validate the coupon code
            $code = $_POST['code'] ?? '';
            $sql = $conn->prepare("SELECT * FROM coupon WHERE discountValidate=1 ORDER BY discount DESC");
            $sql->execute();
            $discountResult = $sql->get_result();

            if ($discountResult->num_rows > 0) {
                while ($rowDiscount = $discountResult->fetch_assoc()) {
                    if ($rowDiscount['discountCode'] == $code) {
                        $_SESSION['couponDiscount'] = 1 - ($rowDiscount['discount'] / 100);
                        $_SESSION['discountCode'] = $rowDiscount['discountCode'];
                        break;
                    }
                }
            }
        }


        ?>

    </div>

    <a class="nextPage" href="index.php">Back</a>
    <?php if ($_GET['veterinarian'] > 1) echo "<a class=\"nextPage\" href=\"book_apointment.php?veterinarian=" . ($_GET['veterinarian'] - 1) . "\">Previous table</a>"; ?>
    <?php if ($_GET['veterinarian'] < $_SESSION['maxVeterinarianId']) echo "<a class=\"nextPage\" href=\"book_apointment.php?veterinarian=" . ($_GET['veterinarian'] + 1) . "\">Next table</a>";


    echo "<br><br><h2>Reservation for table " . $_GET['veterinarian'] . "</h2>";


    $today = date("Y-m-d");
    $sql = $pdo->prepare("SELECT reservation.* FROM pet
        INNER JOIN reservation ON pet.petId = reservation.petId
        WHERE reservation.reservationDay >= :today AND pet.petId = :petId
        ORDER BY reservation.reservationDay ASC;");
     $sql->bindValue(':today', $today, PDO::PARAM_STR);
    $sql->bindValue(':petId', $petID, PDO::PARAM_INT);

    // Execute the query
    $sql->execute();
    $result = $sql->fetchAll(PDO::FETCH_ASSOC);

    // Check the number of rows returned
    if (count($result) < 5) {
        // Fewer than 5 reservations

        if (isset($_POST['action']) && $_POST['action'] == "Reserve" && $_SESSION['reservationTime'] != " " && $_POST['reservationTimeEnd'] != "Select Time") {
            // Prepare and execute the first query

            $_SESSION['reservation'] = 1;
            $_SESSION['reservationCode'] = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
            // Clear the result set
            $result->free();
            if(isset($_SESSION['discountCode'])) {
                // Prepare and execute the insert query
                $insert_sql = $conn->prepare("INSERT INTO reservation (tableId, userId, reservationDay, reservationTime, period,reservationCode,discount) VALUES (?, ?, ?, ?,?, ?,?)");
                $insert_sql->bind_param("iisssii", $_GET['table'], $_SESSION['userID'], $_POST['day'], $_SESSION['reservationTime'],
                    $_POST['reservationTimeEnd'],$_SESSION['reservationCode'],$_SESSION['discountCode']);
            }
            else{
                $insert_sql = $conn->prepare("INSERT INTO reservation (tableId, userId, reservationDay, reservationTime, period,reservationCode) VALUES (?, ?, ?, ?, ?,?)");
                $insert_sql->bind_param("iisssi", $_GET['table'], $_SESSION['userID'], $_POST['day'], $_SESSION['reservationTime'], $_POST['reservationTimeEnd'],$_SESSION['reservationCode'],);

            }
            $insert_sql->execute();

            // Check for successful insertion
            if ($insert_sql->affected_rows > 0) {
                $_SESSION['reservationMessage'] = "You reserved the table " . $_GET['table'] . "!";
                $_SESSION['reservation'] = 1;
                $_SESSION['reservationTable'] = $_GET['table'];
                $_SESSION['reservationTimeEnd'] = $_POST['reservationTimeEnd'];
                $_SESSION['day'] = $_POST['day'];
                header('location:mail.php');
                exit();

            } else {
                $_SESSION['reservationMessage'] = "Error reserving the table. Please try again.";
            }

            $insert_sql->close();
        } elseif (isset($_POST['reservationTimeEnd']) && $_POST['reservationTimeEnd'] == "Select Time") {
            $_SESSION['reservationMessage'] = $_SESSION['reservationMessage']. "<br>You have to select when do you want your reservation to end.";
            unset($_POST['reservationTimeEnd']);
        }
        $_SESSION['reservationTime'] = " ";


        if (!isset($_SESSION['email'])) {
            header('location:index.php');
            exit();
        }

        echo '<label for="res" class="fix">Reservation Date:</label><br><br>';
        if (!isset($_POST['day'])) {
            echo '<input type="date" style="width: 240px" class="inputok" name="day" id="day" onchange="activateSubmit()">';
        } else {
            echo "<input type=\"date\" name=\"day\" class=\"inputok\"id=\"day\" value=\"" . $_POST['day'] . "\" onchange=\"activateSubmit()\">";
        }

        echo "<input type='submit' name='action' id='submitButton' value='day' style='display: none;'><br>";

        if (!isset($_GET['veterinarian'])) {
            header('location:book_veterinarian.php');
            exit();
        }

        if (isset($_POST['action']) && ($_POST['action'] == "TimeStart" || $_POST['action'] == "day")) {
            $today = date("Y-m-d");
            $sqlReservation = $conn->prepare("SELECT * FROM reservation where userId = ? and 
                    reservationDay= ? and tableId= ? ORDER BY reservationDay ASC, reservationTime ASC;");
            $sqlReservation->bind_param('isi', $_SESSION['userID'], $_POST['day'], $_GET['table']);
            $sqlReservation->execute();
            $reservation = $sqlReservation->get_result();
            if ($_POST['day'] >= $today && $reservation->num_rows == 0) {
                $date = " ";
                $hour = " ";
                $period = " ";
                $start = "15:00";
                $end = "23:30";

                if (isset($_POST['reservationTimeStart']))
                    $_SESSION['reservationTime'] = $_POST['reservationTimeStart'];

                $tStart = strtotime($start);
                $tEnd = strtotime($end);
                $tNow = $tStart;
                global $conn;

                if (isset($_POST['day'])) {
                    $day = $_POST['day'];
                    echo "<h2>" . $_POST['day'] . "</h2>";
                }

                $sql = mysqli_prepare($conn, "SELECT * FROM `reservation` WHERE `tableId` = ? and reservationDay=? order by reservationTime asc ");
                $sql->bind_param("is", $_GET['table'], $day);
                $sql->execute();
                $result = $sql->get_result();

                echo '<label for="res">Reservation time:</label><br><label for="res">From:</label>';
                echo "<form method=\"post\" action=\"reservation.php?table=" . $_GET['table'] . "\" class=\"reservationForm\">";
                echo '<select class="inputok" name="reservationTimeStart" onchange="activateSubmit2()"><br>';

                if (!isset($_SESSION['reservationTime']) || $_SESSION['reservationTime'] == " ")
                    echo '<option hidden>Select Time</option>';

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $hour = $row['reservationTime'];
                        $periodHour = $row['period'];

                        if ($hour != " ") {
                            while ($tNow < strtotime($hour) - 45 * 60) {
                                if ($_SESSION['reservationTime'] == date("H:i", $tNow))
                                    echo '<option selected value="' . date("H:i", $tNow) . '">' . date("H:i", $tNow) . '</option>';
                                else
                                    echo '<option value="' . date("H:i", $tNow) . '">' . date("H:i", $tNow) . '</option>';
                                $tNow = strtotime('+15 minutes', $tNow);
                            }
                            $tNow = strtotime($periodHour);
                        }
                    }
                }

                while ($tNow < $tEnd - 45 * 60) {
                    if ($_SESSION['reservationTime'] == date("H:i", $tNow))
                        echo '<option selected value="' . date("H:i", $tNow) . '">' . date("H:i", $tNow) . '</option>';
                    else
                        echo '<option value="' . date("H:i", $tNow) . '">' . date("H:i", $tNow) . '</option>';
                    $tNow = strtotime('+15 minutes', $tNow);
                }

                echo "</select><br><input type='submit' name='action' id='submit2' hidden='hidden' value='TimeStart'><br><br>";
            } elseif ($reservation->num_rows > 0) {
                $_SESSION['reservationMessage'] = "You have reservation on table " . $_GET['table'] . " for day " . $today .
                    ".You can not have to reservations on same day with the same table.";
            } else {
                $_SESSION['reservationMessage'] = "This day has passed, you can't reserve tables before " . $today . ".";
            }
            if (isset($_POST['action']) && $_POST['action'] == "TimeStart") {
                $todayHour = date("H:i:s");
                if (strtotime($_SESSION['reservationTime']) <= strtotime($todayHour) && $today == $_POST['day']) {
                    $_SESSION['reservationMessage'] = "This hour has passed, you can't reserve tables before " . $todayHour . "";
                } else {
                    $tNow = strtotime($_SESSION['reservationTime']) + 60 * 60; // Adding 30 minutes to $tNow
                    $isIncremented = 0;
                    $count = 0;

                    $sql = mysqli_prepare($conn, "SELECT * FROM `reservation` WHERE `tableId` = ? and reservationDay=? order by reservationTime asc ");
                    $sql->bind_param("is", $_GET['table'], $_POST['day']);
                    $sql->execute();
                    $result = $sql->get_result();

                    echo '<label for="res">Reservation time:</label><br><label for="res">To:</label>';
                    echo "<form method=\"post\" action=\"reservation.php?table=" . $_GET['table'] . "\" class=\"reservationForm\">";
                    echo '<select class="inputok" name="reservationTimeEnd"><br>';
                    echo '<option hidden>Select Time</option>';

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $hour = $row['reservationTime'];

                            while ($tNow <= strtotime($hour) && $count < 21) {
                                echo '<option value="' . date("H:i", $tNow) . '">' . date("H:i", $tNow) . '</option>';

                                $tNow = strtotime('+15 minutes', $tNow);
                                $count++;
                                $isIncremented = 1;

                            }

                        }
                    }

                    if ($isIncremented == 0) {
                        if ($tNow < strtotime($_SESSION['reservationTime']) + 60 * 60)
                            $tNow = strtotime($_SESSION['reservationTime']) + 60 * 60;
                        while ($tNow <= $tEnd && $count < 21) {
                            $count++;
                            echo '<option value="' . date("H:i", $tNow) . '">' . date("H:i", $tNow) . '</option>';

                            $tNow = strtotime('+15 minutes', $tNow);
                        }
                    }


                    echo '</select><br><input class="inputok" type="submit" name="action" value="Reserve"><br>';
                }


            }
        }
    } else {
        if(isset($_SESSION['reservationMessage']))
            $_SESSION['reservationMessage'] = $_SESSION['reservationMessage'] . "<br>You have too many reservations, you can't reserve more tables today.";
        else
            $_SESSION['reservationMessage'] = "You have too many reservations, you can't reserve more tables today.";
    }
    if (isset($_SESSION['reservationMessage'])) {
        echo '<div style="display: flex; justify-content: center; align-items: center; margin: 20px 0;">';
        echo '<div style="background-color: #eaf4fc; color: #31708f; border: 1px solid #bce8f1; padding: 15px; border-radius: 5px; width: 80%; text-align: center; font-size: 24px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">';
        echo $_SESSION['reservationMessage'];
        unset($_SESSION['reservationMessage']);
        echo ' <br><button class="inputok" onclick="refreshPage()">Okay</button><br>';
        echo '</div>';
        echo '</div>';
    }
    $t = time();
    $currentTime = date("Y-m-d", $t);
    if ($_SESSION['privalage'] == "admin" || $_SESSION['privalage'] == "worker") {
        $sql = mysqli_prepare($conn, "SELECT CONCAT(user.firstName, ' ', user.lastName) AS name, reservation.*
        FROM user
        INNER JOIN reservation ON user.userId = reservation.userId
        WHERE reservation.reservationDay >= ? AND reservation.tableId = ?
        ORDER BY reservation.reservationDay ASC;");
        $sql->bind_param('si', $currentTime, $_GET['table']);
    } else {
        $sql = mysqli_prepare($conn, "SELECT * FROM reservation where userId = ? and reservationDay>= ? ORDER BY reservationDay ASC,reservationTime ASC;");
        $sql->bind_param('is', $_SESSION['userID'], $currentTime);
    }
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows > 0) {

        echo '<div style="display: flex; justify-content: center; align-items: center;">';
        echo '<br><br><table border="1" style="border-collapse: collapse; width: 90%; margin: 20px auto; font-size: 18px; text-align: left; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">';

        // Table header
        echo '<thead style="background-color: #f8f9fa;">';
        echo '<tr>';
        if ($_SESSION['privalage'] != "admin" && $_SESSION['privalage'] != "worker") {
            echo '<th style="padding: 12px; border: 1px solid #ddd;">Table ID</th>';
        } else {
            echo '<th style="padding: 12px; border: 1px solid #ddd;">Name</th>';
        }
        echo '<th style="padding: 12px; border: 1px solid #ddd;">Reservation Day</th>';
        echo '<th style="padding: 12px; border: 1px solid #ddd;">Reservation Time - Period</th>';
        echo '<th style="padding: 12px; border: 1px solid #ddd;">Code</th>';
        echo '<th style="padding: 12px; border: 1px solid #ddd;">Cancel</th>';
        echo '</tr>';
        echo '</thead>';

        // Table body
        echo '<tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            if ($_SESSION['privalage'] != "admin" && $_SESSION['privalage'] != "worker") {
                echo '<th style="padding: 10px; border: 1px solid #ddd;">' . $row['tableId'] . '</th>';
            } else {
                echo '<th style="padding: 10px; border: 1px solid #ddd;">' . $row['name'] . '</th>';
            }
            echo '<th style="padding: 10px; border: 1px solid #ddd;">' . $row['reservationDay'] . '</th>';
            echo '<th style="padding: 10px; border: 1px solid #ddd;">' . $row['reservationTime'] . ' - ' . $row['period'] . '</th>';
            echo '<th style="padding: 10px; border: 1px solid #ddd;">' . $row['reservationCode'] . '</th>';
            echo '<th style="padding: 10px; border: 1px solid #ddd;">
    <form method="post" action="book_apointment.php?veterinarian=' . $_GET['veterinarian'].'" >
    <input type="submit" name="delete" value="delete" class="inputok" onclick="confirmDeletion();">
    <input type="hidden" name="reservation" value="' . $row['reservationId'] . '">
    </form>
    <script type="text/javascript">
    function confirmDeletion() {
    return confirm("Are you sure you want to delete this reservation?");
        }
    </script>
    </th>';
            echo '</tr>';
        }
        echo '</tbody>';

        echo '</table><br>';



    } else{
        $_SESSION['noTablesMessage'] = "There are no reservations yet";
        echo '<div style="display: flex; justify-content: center; align-items: center; margin: 20px 0;">';
        echo '<div style="background-color: #eaf4fc; color: #31708f; border: 1px solid #bce8f1; padding: 15px; border-radius: 5px; width: 80%; text-align: center; font-size: 24px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">';
        echo $_SESSION['noTablesMessage'];
        unset($_SESSION['noTablesMessage']);
        echo '</div>';
        echo '</div>';
    }



    $sql->close();
    echo '</div>';
    echo '</form>';

    ?>

</body>
</html>
