<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modify Profile</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
<form method="post" action="functions.php" class="mainForm">
    <a class="nextPage" href="index.php">Back</a><br><br>
    <h1 >Modify User</h1>
    <input type="hidden" placeholder="Emailcím" name="mail" class="inputok" id="mail" value="<?php
    if(isset($_SESSION['email'])){echo $_SESSION['email'];}
    ?>">
    <label for="knev">Keresztnev:</label><br>
    <input type="text" class="inputok" placeholder="Keresztnév" name="firstName" id="knev"><br>
    <label for="vnev">Vezeteknev:</label><br>
    <input type="text" class="inputok" placeholder="Vezetéknév" name="lastName" id="vnev"><br>

    <label for="tel2">Telefonszám:</label><br>
    <select name="tel1" class="inputok">
        <?php
        for ($i=10; $i<=39; $i++){

            echo "<option value=\"0".$i."\">0".$i."</option>";

            if($i==23 || $i==28 ||$i==29 || $i==39){
                echo "<option value=\"0".$i."\"0>0".$i."0</option>";
            }

        }
        for ($i=60; $i<=69; $i++){

            echo "<option value=\"0".$i."\">0".$i."</option>";


        }
        ?>

    </select>
    <input type="text"  placeholder="Telefonszám" name="tel2" class="inputok"  id="tel2"><br>

    <input type="submit" name="action" value="ModifyUser" class="inputok"><br><br>

    <?php
    include "config.php";
    global $conn;

    $message=isset($_SESSION['message']) ? $_SESSION['message']:'';
    if(isset($_SESSION['message'])) {
        echo "<p class='warning'>" . $message . "</p>";


    }?>

</form>
</body>
</html>