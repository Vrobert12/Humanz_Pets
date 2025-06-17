<?php
include "functions.php";
$autoload = new Functions();
$lang = $autoload->language();
$autoload->checkAutoLogin();

if($_SESSION['backPic']=="registerAnimal.php" || $_SESSION['backPic']=="pet.php")
$_SESSION['backPic']='index.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Makes it responsive -->
    <title>Modify Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Optional: Custom Styles -->
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: #659df7">
<div class="container my-5">
    <form method="post" action="functions.php" class="mainForm">
        <!-- Back Link -->
        <div class="mb-3">
            <a class="btn btn-secondary" href="<?php echo $_SESSION['backPic'] ?>"><?php echo BACK ?></a>
        </div>

        <?php
        echo '<h1 class="text-center mb-4">' . SETTINGS . '</h1>';
        if (isset($_GET['veterinarianId']) && $_SESSION['privilage'] == 'Admin')
            $autoload->modifyForAdmin('veterinarian', $_GET['veterinarianId']);
         elseif (isset($_GET['userId']) && $_SESSION['privilage'] == 'Admin')
            $autoload->modifyForAdmin('user', $_GET['userId']);
         else {
            echo '<input type="hidden" name="mail" class="form-control" id="mail" value="' . $_SESSION['email'] . '">';

            echo '  <div class="mb-3">
            <label for="knev" class="form-label">' . NAME . ':</label>
            <input type="text" class="form-control" placeholder=" '.NAME.' " value="' . $_SESSION['firstName'] . '" name="firstName" id="knev">
        </div>';

            echo ' <div class="mb-3">
            <label for="vnev" class="form-label">' . LASTNAME . ':</label>
            <input type="text" class="form-control" placeholder="'. LASTNAME .'" value="' . $_SESSION['lastName'] . '" name="lastName" id="vnev">
        </div>';

            echo '<div class="mb-3">
            <label for="tel" class="form-label">' . PHONE . ':</label>
            <div class="input-group">

                <input type="text" class="form-control" placeholder="<'. PHONE .'" value="' . $_SESSION['phone'] . '" name="tel" id="tel2">
            </div>';
        } ?>


<div class="mb-3">
    <label for="usedLanguage" class="form-label"><?php echo LG; ?>:</label>
    <div class="input-group">
        <select name="usedLanguage">
            <option hidden="SELECT_LANG" value="SELECT_LANG"><?php echo LG?></option>
            <option value="en"><?php echo LANGUAGE_en; ?></option>
            <option value="hu"><?php echo LANGUAGE_hu; ?></option>
            <option value="sr"><?php echo LANGUAGE_sr; ?></option>
        </select>
    </div>
</div>

<!-- Save Button -->
<div class="mb-3 text-center">
    <input type="submit" name="submit" value="<?php echo SAVE ?>" class="btn btn-primary">
</div>

<!-- Hidden Input for Action -->
<input type="hidden" name="action" value="ModifyUser">

<!-- Warning Message -->
<?php
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
if (!empty($message)) {
    echo "<div class='alert alert-warning text-center'>" . $message . "</div>";
}
?>
</form>
</div>
</div>
<!-- Bootstrap JS (for responsive utilities like modals, dropdowns) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
