<?php
session_start();
$lang = $_GET['lang'] ?? 'en';
if(isset($_GET['lang'])){
    $_SESSION['lang'] = $_GET['lang'];
}
include_once "lang_$lang.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <?php include "config.php"; ?>
    <script src="indexJS.js"></script>
    <style>
        .warning {
            color: red;
        }
        .inputok {
            border-radius: 10px;
            font-size: 20px;
            padding: 10px;
            margin: 10px;
            text-align: center;
        }
        .inputok.error {
            border-color: red;
        }
    </style>

</head>
<body>

<?php

if (isset($_SESSION['token']) && isset($_GET['token'])) {
    if ($_SESSION['token'] != $_GET['token']) {
        header('location:' . $_SESSION['previousPage']);
        $_SESSION['title'] = "";
        exit();
    } else {
        $_SESSION['token'] = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
    }
} else {
    echo '<form method="post" action="functions.php" class="mainForm" enctype="multipart/form-data">';
}
if (isset($_SESSION['title'])) {
    echo "<h2 style='color: #2a7e2a'>" . $_SESSION['title'] . "</h2>";
}
$_SESSION['backPic']='registerAnimal.php';
?>

    <a class="nextPage" href="index.php">Back</a><br><br>
    <label for="petName"><?php echo NAME?>:</label><br>
    <input type="text" class="inputok" placeholder="<?php echo NAME?>" name="petName" id="petName" ><br>
    <label for="bred"><?php echo LASTNAME?>:</label><br>
    <input type="text" class="inputok" placeholder="<?php echo LASTNAME?>" name="bred" id="bred" ><br>

    <label for="specie"><?php echo PHONE?>:</label><br>
    <select name="specie" class="inputok" id="specie">
        <option hidden="hidden" value="specie"><?php echo NUMBER?></option>
        <option value="dog">dog</option>
        <option value="cat">cat</option>
        <option value="parrot">parrot</option>
        <option value="bunny">bunny</option>
        <option value="pig">pig</option>
    </select>

    <img src="/Humanz2.0/pictures/logInPic.png" alt="img" width="32" height="32" onclick="activateProfilePicture()" style="cursor: pointer;">
    <input  type='file' name='picture' id='pictureInput' style='display: none;' accept='image/*'>
    <input type='submit' class="inputok"  name='submit' id='submitButton' value='Register' >
    <input type="hidden" name="action" value="registerAnimal"><br>
<?php
if(isset($_SESSION['message'])) {
    echo $_SESSION['message'];
}
?>
</form>

</body>
</html>
