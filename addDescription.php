<?php
include "functions.php";
$autoload=new Functions();
$lang=$autoload->language();
$autoload->checkAutoLogin();
$pdo = $autoload->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);
if($_SESSION['privilage']!='Veterinarian'){
    header('Location:index.php');
    exit();
}
?>
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

$_SESSION['backPic']='addProduct.php';
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
<h3><?php echo ADD_DESCRIPTION_VET?>:</h3><br>
<?php

$sql="Select veterinarianDescription from veterinarian WHERE veterinarianId=:veterinarianId";
$sql=$pdo->prepare($sql);
$sql->bindValue(':veterinarianId',$_SESSION['userId']);
$sql->execute();
$result = $sql->fetch(PDO::FETCH_ASSOC);
?>
<textarea class="inputok" placeholder="<?php echo DESCRIPTION_VET?>" name="vetDescription" id="vetDescription" rows="6" cols="50">
    <?php echo $result['veterinarianDescription']?>
</textarea><br>

<input type='submit' class="btn btn-primary"  name='submit' id='submitButton' value='<?php echo ADD?>' >

<input type="hidden" name="action" value="insertDescription"><br>
<?php
if(isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
</form>

</body>
</html>
