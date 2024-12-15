<?php
include "functions.php";
$autoload = new Functions();
$autoload->checkAutoLogin();
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
include "lang_$lang.php";
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
<body>
<div class="container my-5">
    <form method="post" action="functions.php" class="mainForm">
        <!-- Back Link -->
        <div class="mb-3">
            <a class="btn btn-secondary" href="index.php"><?php echo BACK ?></a>
        </div>

        <!-- Heading -->
        <h1 class="text-center mb-4"><?php echo SETTINGS ?></h1>

        <!-- Email (Hidden Input) -->
        <input type="hidden" name="mail" class="form-control" id="mail" value="<?php if (isset($_SESSION['email'])) { echo $_SESSION['email']; } ?>">

        <!-- First Name -->
        <div class="mb-3">
            <label for="knev" class="form-label"><?php echo NAME ?>:</label>
            <input type="text" class="form-control" placeholder="<?php echo NAME ?>" name="firstName" id="knev">
        </div>

        <!-- Last Name -->
        <div class="mb-3">
            <label for="vnev" class="form-label"><?php echo LASTNAME ?>:</label>
            <input type="text" class="form-control" placeholder="<?php echo LASTNAME ?>" name="lastName" id="vnev">
        </div>

        <!-- Phone Number -->
        <div class="mb-3">
            <label for="tel2" class="form-label"><?php echo PHONE ?>:</label>
            <div class="input-group">
                <!-- Dropdown for Country Code -->
                <select name="tel1" class="form-select">
                    <?php
                    for ($i = 10; $i <= 39; $i++) {
                        echo "<option value=\"0".$i."\">0".$i."</option>";
                        if ($i == 23 || $i == 28 || $i == 29 || $i == 39) {
                            echo "<option value=\"0".$i."0\">0".$i."0</option>";
                        }
                    }
                    for ($i = 60; $i <= 69; $i++) {
                        echo "<option value=\"0".$i."\">0".$i."</option>";
                    }
                    ?>
                </select>
                <!-- Phone Number Input -->
                <input type="text" class="form-control" placeholder="<?php echo PHONE ?>" name="tel2" id="tel2">
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

<!-- Bootstrap JS (for responsive utilities like modals, dropdowns) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
