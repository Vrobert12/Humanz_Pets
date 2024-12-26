<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    $lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
    if(isset($_GET['lang'])){
        $_SESSION['lang'] = $_GET['lang'];
    }
    include "lang_$lang.php";
}

include "functions.php";
$autoload=new Functions();
$autoload->checkAutoLogin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Main Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=yes">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script>
        const lang = '<?php echo $lang; ?>';
    </script>
    <script src="LogOut.js"></script>
    <script src="indexJS.js"></script>
    <link rel="stylesheet" href="style.css">


</head>
<body>

<!-- Show popup message if session message is set -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="popup-message" id="popupMessage">
        <?php echo $_SESSION['message']; ?>
    </div>
    <?php unset($_SESSION['message']); // Clear message after it's displayed ?>
<?php endif; ?>

<script>

</script>

<!-- Show popup message if session message is set -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="popup-message" id="popupMessage">
        <?php echo $_SESSION['message']; ?>
    </div>
    <?php unset($_SESSION['message']); // Clear message after it's displayed ?>
<?php endif; ?>

<script>
    // Show the popup message and hide it after 5 seconds
    window.onload = function () {
        var popupMessage = document.getElementById('popupMessage');
        if (popupMessage) {
            popupMessage.style.display = 'block';  // Show the popup

            // Hide the popup after 5 seconds
            setTimeout(function () {
                popupMessage.style.display = 'none';
            }, 5000);
        }
    };
</script>
<!--
https://getbootstrap.com/docs/5.3/components/navbar/
-->
<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="#">Logo</a>
        <!-- Toggler for mobile view -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <!-- Centered Navigation Links -->
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="contacts.php"></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"></a>
                </li>
                <li class="nav-item">
                    <?php
                    $sql = "SELECT * FROM user";
                    ?>
                    <a class="nav-link" href="book_veterinarian.php"><i class="bi bi-book-fill fs-3"></i></a>
                </li>
            </ul>
            <!-- Language Dropdown (Aligned Right) -->
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php echo LG ?>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="?lang=en"><?php echo LANGUAGE_en ?></a>
                    <a class="dropdown-item" href="?lang=hu"><?php echo LANGUAGE_hu ?></a>
                    <a class="dropdown-item" href="?lang=sr"><?php echo LANGUAGE_sr ?></a>
                </div>
            </div>
        </div>
    </div>
</nav>


<div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark d-none d-md-inline-block" style=" top: 0; bottom: 0; width: 280px;">
    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"/></svg>
        <span class="fs-4">Sidebar</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="#" class="nav-link active" aria-current="page">
                <svg class="bi me-2" width="16" height="16"><use xlink:href="#home"/></svg>
                Home
            </a>
        </li>
        <?php
        if(isset($_SESSION['email'])){
        echo '<li>
        <a href="pet.php?email=' . $_SESSION['email'] . '" class="nav-link text-white">
            <svg class="bi me-2" width="16" height="16">
                <use xlink:href="pet.php?email=' . urlencode($_SESSION['email']) . '" />
            </svg>
            '.MYPETS.'
        </a>
    </li>';

        echo '<li>
        <a href="registerAnimal.php" class="nav-link text-white">
            <svg class="bi me-2" width="16" height="16">
                <use xlink:href="registerAnimal.php" />
            </svg>
            '.ADDPET.'
        </a>
    </li>';

      echo'  <li>
            <a href="products.php" class="nav-link text-white">
                <svg class="bi me-2" width="16" height="16"><use xlink:href="#grid"/></svg>
              '.PRODUCT.'
            </a>
        </li>';
            if ($_SESSION['privilage'] == 'Admin') {
      echo'  <li>
            <a href="veterinarians.php" class="nav-link text-white">
                <svg class="bi me-2" width="16" height="16"><use xlink:href="#grid"/></svg>
                '.VETS.'
            </a>
        </li>';

        echo'<li>
            <a href="users.php" class="nav-link text-white">
                <svg class="bi me-2" width="16" height="16"><use xlink:href="#people-circle"/></svg>
                '.USERS.'
            </a>
        </li>';
        echo'<li>
            <a href="addVet.php" class="nav-link text-white">
                <svg class="bi me-2" width="16" height="16"><use xlink:href="#people-circle"/></svg>
                Add veterinarian
            </a>
        </li>';
        }
          }

        ?>
    </ul>
    <hr>
    <div class="dropdown">
            <?php
            echo '<div class="d-none d-lg-block d-md-block dropdown">';
            if (isset($_SESSION['email'])) {
                $_SESSION['action'] = "kijelentkezes";

// Combined profile picture, text, and dropdown toggle in a single line
                echo '<a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false" style="color: white;">';
                echo '<img src="pictures/' . $_SESSION['profilePic'] . '" alt="img" width="32" height="32" class="rounded-circle me-2" onclick="activateProfilePicture()" style="cursor: pointer;">';
                echo '<strong>' . (isset($_SESSION['name']) ? $_SESSION['name'] : ACCOUNT) . '</strong>';
                echo '</a>';

                // Dropdown menu
                echo '<ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">';
                echo '<li><a class="dropdown-item" href="modify.php"><i class="fas fa-gear"></i>&nbsp'.SETTINGS.'</a></li>';
                echo '<li><a class="dropdown-item" href="userData.php?email='.$_SESSION['email'].'"><i class="fas fa-circle-info"></i>&nbsp'.ACCOUNT.'</a></li>';
                echo '<li><hr class="dropdown-divider"></li>';
                echo '<li><a class="dropdown-item" href="functions.php?action=logOut" onclick="confirmLogout(event)">';
                echo '<i class="bi bi-door-open fa-2x justify-content-end"></i>'.LOGOUT.'</a></li>';
                echo '</ul>';

                // Hidden form for file upload
                echo "<form method='post' action='functions.php' enctype='multipart/form-data' style='display: none;'>";
                $_SESSION['backPic'] = "index.php";
                echo "<input class='dropdown-item' type='file' name='picture' id='pictureInput' style='display: none;' accept='image/*' onchange='activateSubmitUser()'>";
                echo "<input type='submit' name='action' id='submitButton' value='picture' style='display: none;'>";
                echo '</form>';

                echo '</div>';
            } else {
                // If not logged in, show a default profile picture and dropdown for login/register

                echo '<a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">';
                echo '<img src="pictures/logInPic.png" alt="img" width="32" height="32" class="rounded-circle me-2">';
                echo '<strong>'.ACCOUNT.'</strong>';
                echo '</a>';
                echo '<ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">';
                echo '<li><a class="dropdown-item" href="logIn.php">'.LOGIN.'</a></li>';
                echo '<li><a class="dropdown-item" href="registration.php">'.REGISTER.'</a></li>';
                echo '</ul>';
                echo '</div>';
            }
            ?>


    </div>
</div>
<div class="d-flex flex-column flex-shrink-0 bg-dark d-block d-md-none" style="width: 4.5rem; min-height: 100vh;">
    <div class="d-flex flex-column flex-shrink-0 bg-dark " style="width: 4.5rem;">
        <a href="/" class="d-flex align-items-center justify-content-center p-3 link-light text-decoration-none" title="" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Home" style="background: #007bff">
            <i class="bi bi-house-door" style="font-size: 24px;"></i>
            <span class="visually-hidden">Home</span>
        </a>

        </ul>
        <?php
echo '<div class="dropdown border-top">';
        if(isset($_SESSION['email'])){
            echo '
        <ul class="nav nav-pills nav-flush flex-column text-center">
            <li class="nav-item">
                <a href="pet.php?email=' . urlencode($_SESSION['email']) . '" class="d-flex align-items-center justify-content-center p-3 link-light text-decoration-none" title="" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Home">
                    <i class="fas fa-dog" style="font-size: 24px;"></i>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>
            <li>
                <a href="registerAnimal.php" class="d-flex align-items-center justify-content-center p-3 link-light text-decoration-none" title="" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Home">
                    <i class="fas fa-clipboard" style="font-size: 24px;"></i>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>';
            if($_SESSION['privilage'] == 'Admin') {
            echo'
            <li>
                <a href="products.php" class="d-flex align-items-center justify-content-center p-3 link-light text-decoration-none" title="" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Home">
                    <i class="fas fa-dolly" style="font-size: 24px;"></i>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>
            <li>
                <a href="veterinarians.php" class="d-flex align-items-center justify-content-center p-3 link-light text-decoration-none" title="" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Home">
                    <i class="fas fa-stethoscope" style="font-size: 24px;"></i>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>
             <li>
                <a href="users.php" class="d-flex align-items-center justify-content-center p-3 link-light text-decoration-none" title="" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Home">
                    <i class="bi bi-people-fill" style="font-size: 24px;"></i>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>';}
            echo '</ul>';
            $_SESSION['action'] = "kijelentkezes";
            echo '<a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false" style="color: white;">';
            echo '<img src="pictures/' . $_SESSION['profilePic'] . '" alt="img" width="32" height="32" class="rounded-circle me-2" onclick="activateProfilePicture()" style="cursor: pointer;">';

            echo '</a>';

            // Dropdown menu
            echo '<ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">';
            echo '<li><a class="dropdown-item" href="modify.php"><i class="fas fa-gear"></i>&nbspSettings</a></li>';
            echo '<li><a class="dropdown-item" href="userData.php?email='.$_SESSION['email'].'"><i class="fas fa-circle-info"></i>&nbspProfile</a></li>';
            echo '<li><hr class="dropdown-divider"></li>';
            echo '<li><a class="dropdown-item" href="functions.php?action=logOut" onclick="confirmLogout(event)">';
            echo '<i class="bi bi-door-open fa-2x justify-content-end"></i> Log out</a></li>';
            echo '</ul>';

            // Hidden form for file upload
            echo "<form method='post' action='functions.php' enctype='multipart/form-data' style='display: none;'>";
            $_SESSION['backPic'] = "index.php";
            echo "<input class='dropdown-item' type='file' name='picture' id='pictureInput' style='display: none;' accept='image/*' onchange='activateSubmitUser()'>";
            echo "<input type='submit' name='action' id='submitButton' value='picture' style='display: none;'>";
            echo '</form>';

            echo '</div>';
        }
        else{
            echo '<div class="d-flex justify-content-center">'; // Center the entire block
            echo '  <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">';
            echo '    <img src="pictures/logInPic.png" alt="img" width="32" height="32" class="rounded-circle me-2">';
            echo '  </a>';
            echo '  <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">';
            echo '    <li><a class="dropdown-item" href="logIn.php">Log in</a></li>';
            echo '    <li><a class="dropdown-item" href="registration.php">Register</a></li>';
            echo '  </ul>';
            echo '</div>';

        }
        ?>
    </div>
    </div>
</div>

</body>
</html>
