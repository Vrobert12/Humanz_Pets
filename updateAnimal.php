<?php
session_start();
include "functions.php";
$autoload = new Functions();
$_SESSION['backPic']="updateAnimal.php";
$autoload->checkAutoLogin('updateAnimal.php');
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
include_once "lang_$lang.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
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
$_SESSION['backPic'] = 'registerAnimal.php';


if (isset($_GET['petUpdate'])){
    echo '<input type="hidden"  name="petUpdate" id="petName" value="'. $_GET['petUpdate'].'"><br>';
    $_SESSION['petId'] = $_GET['petId'];
    $_SESSION['petPicture'] = $_GET['petPicture'];
    echo '<a class="btn btn-secondary" href="pet.php?email='.$_SESSION['email'].'"> '.BACK.'</a><br><br>';
}
else
    echo '<a class="btn btn-secondary" href="index.php"> '.BACK.'</a><br><br>';
?>

<label for="petName"><?php echo NAME?>:</label><br>
<input type="text" class="inputok" placeholder="<?php echo NAME?>" name="petName" id="petName" value="<?php echo $_GET['petName']?>"><br>
<label for="bred"><?php echo BREED?>:</label><br>
<input type="text" class="inputok" placeholder="<?php echo BREED?>" name="bred" id="bred" value="<?php echo $_GET['bred']?>"><br>

<label for="specie"><?php echo SPECIES?>:</label><br>
<select name="specie" class="inputok" id="specie">
    <option hidden="hidden" value="<?php echo $_GET['petSpecies']?> " selected><?php echo $_GET['petSpecies']?></option>
    <option value="dog"><?php echo DOG?></option>
    <option value="cat"><?php echo CAT?></option>
    <option value="parrot"><?php echo PARROT?></option>
    <option value="bunny"><?php echo BUNNY?></option>
    <option value="pig"><?php echo PIG?></option>
</select>

<!-- Displaying the existing profile picture -->
<img id="productImage" src="/Humanz2.0/pictures/<?php echo $_GET['petPicture']?>" alt="Profile Image" width="100" height="100" onclick="activateProfilePicture()"
     style="cursor: pointer; opacity: 0.7; transition: opacity 0.3s;"
     onmouseover="this.style.opacity=1;" onmouseout="this.style.opacity=0.7;">

<!-- Hidden file input to allow updating the profile picture -->
<input type="file" name="picture" id="pictureInput" style="display: none;" accept="image/*" onchange="updateImagePreview(event)">

<!-- Submit Button -->
<input type="submit" class="btn btn-primary" name="submit" id="submitButton" value="<?php echo UPDATE?>">

<!-- Hidden fields -->
<input type="hidden" name="action" value="updatePet"><br>

<?php
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

</form>

</body>
</html>