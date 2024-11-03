<?php

session_start();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Task 2</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="LogOut.js"></script>
    <script src="indexJS.js"></script>
</head>
<body>
<!--
https://getbootstrap.com/docs/5.3/components/navbar/
-->
<nav class="navbar navbar-expand-sm bg-dark navbar-dark justify-content-center">
    <div class="navbar-nav ">
        <a class="navbar-brand" href="#">Logo</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="contacts.php">Contats</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li>

            </ul>
        </div>
    </div>
</nav>
<div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark d-none d-md-inline-block" style="position: fixed; top: 0; bottom: 0; width: 280px;">
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
        <li>
            <a href="#" class="nav-link text-white">
                <svg class="bi me-2" width="16" height="16"><use xlink:href="#speedometer2"/></svg>
                Dashboard
            </a>
        </li>
        <li>
            <a href="#" class="nav-link text-white">
                <svg class="bi me-2" width="16" height="16"><use xlink:href="#table"/></svg>
                Orders
            </a>
        </li>
        <li>
            <a href="#" class="nav-link text-white">
                <svg class="bi me-2" width="16" height="16"><use xlink:href="#grid"/></svg>
                Products
            </a>
        </li>
        <li>
            <a href="#" class="nav-link text-white">
                <svg class="bi me-2" width="16" height="16"><use xlink:href="#people-circle"/></svg>
                Customers
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
            <?php
            echo '<div class="d-none d-lg-block d-md-block dropdown">';
            if (isset($_SESSION['email'])) {
                $_SESSION['action'] = "kijelentkezes";

// Combined profile picture, text, and dropdown toggle in a single line
                echo '<a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false" style="color: white;">';
                echo '<img src="/Humanz2.0/pictures/' . $_SESSION['profilePic'] . '" alt="img" width="32" height="32" class="rounded-circle me-2" onclick="activateProfilePicture()" style="cursor: pointer;">';
                echo '<strong>' . (isset($_SESSION['name']) ? $_SESSION['name'] : "Account") . '</strong>';
                echo '</a>';

                // Dropdown menu
                echo '<ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">';
                echo '<li><a class="dropdown-item" href="#">New project...</a></li>';
                echo '<li><a class="dropdown-item" href="#">Settings</a></li>';
                echo '<li><a class="dropdown-item" href="modify.php">Profile</a></li>';
                echo '<li><hr class="dropdown-divider"></li>';
                echo '<li><a class="dropdown-item" href="functions.php" onclick="confirmLogout(event)">';
                echo '<i class="bi bi-door-open fa-2x justify-content-end"></i> Log out</a></li>';
                echo '</ul>';

                // Hidden form for file upload
                echo "<form method='post' action='functions.php' enctype='multipart/form-data' style='display: none;'>";
                $_SESSION['backPic'] = "index.php";
                echo "<input class='dropdown-item' type='file' name='picture' id='pictureInput' style='display: none;' onchange='activateSubmit()'>";
                echo "<input type='submit' name='action' id='submitButton' value='picture' style='display: none;'>";
                echo '</form>';

                echo '</div>';
            } else {
                // If not logged in, show a default profile picture and dropdown for login/register

                echo '<a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">';
                echo '<img src="/Humanz2.0/pictures/logInPic.png" alt="img" width="32" height="32" class="rounded-circle me-2">';
                echo '<strong>Account</strong>';
                echo '</a>';
                echo '<ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">';
                echo '<li><a class="dropdown-item" href="logIn.php">Log in</a></li>';
                echo '<li><a class="dropdown-item" href="registration.php">Register</a></li>';
                echo '</ul>';
                echo '</div>';
            }
            ?>


    </div>
</div>
<div class="d-flex flex-column flex-shrink-0 bg-dark d-block d-md-none" style="width: 4.5rem; min-height: 100vh;">
    <div class="d-flex flex-column flex-shrink-0 bg-dark " style="width: 4.5rem;">
        <a href="/" class="d-flex align-items-center justify-content-center p-3 link-light text-decoration-none" title="" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Home">
            <i class="bi bi-house-door" style="font-size: 24px;"></i>
            <span class="visually-hidden">Home</span>
        </a>

        <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
            <li class="nav-item">
                <a href="/" class="d-flex align-items-center justify-content-center p-3 link-light text-decoration-none" title="" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Home">
                    <i class="bi bi-house-door" style="font-size: 24px;"></i>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>
            <li>
                <a href="/" class="d-flex align-items-center justify-content-center p-3 link-light text-decoration-none" title="" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Home">
                    <i class="bi bi-house-door" style="font-size: 24px;"></i>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>
            <li>
                <a href="/" class="d-flex align-items-center justify-content-center p-3 link-light text-decoration-none" title="" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Home">
                    <i class="bi bi-house-door" style="font-size: 24px;"></i>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>
            <li>
                <a href="/" class="d-flex align-items-center justify-content-center p-3 link-light text-decoration-none" title="" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Home">
                    <i class="bi bi-house-door" style="font-size: 24px;"></i>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>
            <li>
                <a href="/" class="d-flex align-items-center justify-content-center p-3 link-light text-decoration-none" title="" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Home">
                    <i class="bi bi-house-door" style="font-size: 24px;"></i>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>
        </ul>
        <?php
echo '<div class="dropdown border-top ">';
        if(isset($_SESSION['email'])){
            $_SESSION['action'] = "kijelentkezes";
            echo '<a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false" style="color: white;">';
            echo '<img src="/Humanz2.0/pictures/' . $_SESSION['profilePic'] . '" alt="img" width="32" height="32" class="rounded-circle me-2" onclick="activateProfilePicture()" style="cursor: pointer;">';

            echo '</a>';

            // Dropdown menu
            echo '<ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">';
            echo '<li><a class="dropdown-item" href="#">New project...</a></li>';
            echo '<li><a class="dropdown-item" href="#">Settings</a></li>';
            echo '<li><a class="dropdown-item" href="modify.php">Profile</a></li>';
            echo '<li><hr class="dropdown-divider"></li>';
            echo '<li><a class="dropdown-item" href="functions.php" onclick="confirmLogout(event)">';
            echo '<i class="bi bi-door-open fa-2x justify-content-end"></i> Log out</a></li>';
            echo '</ul>';

            // Hidden form for file upload
            echo "<form method='post' action='functions.php' enctype='multipart/form-data' style='display: none;'>";
            $_SESSION['backPic'] = "index.php";
            echo "<input class='dropdown-item' type='file' name='picture' id='pictureInput' style='display: none;' onchange='activateSubmit()'>";
            echo "<input type='submit' name='action' id='submitButton' value='picture' style='display: none;'>";
            echo '</form>';

            echo '</div>';
        }
        else{
            echo '<div class="d-flex justify-content-center">'; // Center the entire block
            echo '  <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">';
            echo '    <img src="/Humanz2.0/pictures/logInPic.png" alt="img" width="32" height="32" class="rounded-circle me-2">';
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
