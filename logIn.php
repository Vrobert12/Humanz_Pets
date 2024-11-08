<?php
session_start();
if(isset($_SESSION['message'])){
    echo $_SESSION['message'];
    unset($_SESSION['message']);
}
$lang = $_GET['lang'] ?? 'en';
if(isset($_GET['lang'])){
    $_SESSION['lang'] = $_GET['lang'];
}else{ $lang = $_SESSION['lang']; }
include_once "lang_$lang.php";
?>
<form method="post" action="functions.php" class="mainForm">
    <a class="nextPage" href="index.php"><?php echo BACK?></a><br><br>
    <label for="mail">E-mail</label><br>
    <input type="email" placeholder="Email" name="mail" class="inputok" id="mail"><br>
    <label for="pass"><?php echo PASSWORD?></label><br>
    <input type="password" placeholder="********" name="pass" class="inputok" id="pass"><br>

    <input type="submit" name="action" value="<?php echo LOGIN?>" class="inputok"><br><br>

    <label for="mail"><?php echo NOACC?></label><br><br>
    <a href="registration.php"><?php echo REGHERE?></a><br>


</form>
