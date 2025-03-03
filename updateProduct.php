<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="ajaxPictureUpdate.js"></script>
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
<body style="background: #659df7">

<?php
session_start();
include "functions.php";
$autoload=new Functions();
$lang=$autoload->language();
$autoload->checkAutoLogin();

$_SESSION['backPic'] = "addProduct.php";
?>

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
$_SESSION['backPic'] = 'addProduct.php';
?>

<a class="btn btn-secondary" href="products.php"><?php echo BACK?></a><br><br>
<label for="productName"><?php echo NAME?>:</label><br>
<input type="text" class="inputok" placeholder="<?php echo NAME?>" name="productName" id="productName" value="<?php echo $_POST['productName']?>"><br>

<label for="price"><?php echo PRICE?>(€):</label><br>
<input type="text" class="inputok" placeholder="<?php echo PRICE?>" name="price" id="price" value="<?php echo $_POST['productCost'];?>"><br>

<label for="productDescription"><?php echo PRODUCTDESCRIPTION?>:</label><br>
<textarea class="inputok" placeholder="<?php echo PRODUCTDESCRIPTION?>"
          name="productDescription" id="productDescription">
    <?php echo htmlspecialchars($_POST['productDescription'] ?? ''); ?></textarea><br>

<!-- Image Preview -->
<?php $_SESSION['updateProductPicture']=$_POST['productPicture']?>
<img id="productImage" src="pictures/products/<?php echo $_POST['productPicture'];?>"
     alt="Product Image" width="150" height="150" onclick="document.getElementById('pictureInput').click();"
     style="cursor: pointer; opacity: 0.7; transition: opacity 0.3s;"
     onmouseover="this.style.opacity=1;" onmouseout="this.style.opacity=0.7;">

<!-- File Input (hidden) -->
<input type="file" name="picture" id="pictureInput" style="display: none;" accept="image/*" onchange="updateImagePreview(event)"><br>

<!-- Submit Button -->
<input type="submit" class="btn btn-primary" name="submit" id="submitButton" value="<?php echo ADD?>">

<!-- Hidden Fields -->
<input type="hidden" class="inputok" name="productId" value="<?php echo $_GET['id'];?>">
<input type="hidden" name="action" value="updateProduct"><br>

<?php
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

</form>

</body>
</html>
