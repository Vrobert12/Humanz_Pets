<?php
session_start();
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
if(isset($_GET['lang'])){
    $_SESSION['lang'] = $_GET['lang'];
}
include "lang_$lang.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modify Profile</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
<form method="post" action="functions.php" class="mainForm">
    <a class="nextPage" href="index.php"><?php echo BACK?></a><br><br>
    <h1><?php echo SETTINGS?></h1>
    <input type="hidden" placeholder="Email" name="mail" class="inputok" id="mail" value="<?php
    if(isset($_SESSION['email'])){echo $_SESSION['email'];}
    ?>">
    <label for="knev"><?php echo NAME?>:</label><br>
    <input type="text" class="inputok" placeholder="<?php echo NAME?>" name="firstName" id="knev"><br>
    <label for="vnev"><?php echo LASTNAME?>:</label><br>
    <input type="text" class="inputok" placeholder="<?php echo LASTNAME?>" name="lastName" id="vnev"><br>

    <label for="tel2"><?php echo PHONE?>:</label><br>
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
    <input type="text"  placeholder="<?php echo PHONE?>" name="tel2" class="inputok"  id="tel2"><br>

    <input type="submit" name="submit" value="<?php echo SAVE?>" class="inputok"><br><br>
    <input type="hidden" name="action" value="ModifyUser" class="inputok">

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