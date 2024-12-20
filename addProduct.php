<?php
session_start();
include "functions.php";
$autoload=new Functions();
$autoload->checkAutoLogin();
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
include_once "lang_$lang.php";
$_SESSION['backPic'] = "addProduct.php";
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
$_SESSION['backPic']='addProduct.php';
?>

<a class="btn btn-secondary" href="index.php"><?php echo BACK?></a><br><br>
<label for="petName"><?php echo NAME?>:</label><br>
<input type="text" class="inputok" placeholder="<?php echo NAME?>" name="productName" id="productName" ><br>

<label for="description"><?php echo DESC?>:</label><br>
<textarea class="inputok" placeholder="<?php echo DESC?>" name="description" id="description" rows="4"></textarea><br>

<label for="bred"><?php echo PRICE?>(€):</label><br>
<input type="text" class="inputok" placeholder="<?php echo PRICE?>" name="price" id="price" ><br>

<input type='submit' class="btn btn-primary"  name='submit' id='submitButton' value='<?php echo ADD?>' >
<input type='hidden' class="inputok"  name='Action' id='submitButton2' value='Register' >
<input type="hidden" name="action" value="addProduct"><br>
<?php
if(isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
</form>

</body>
</html>
