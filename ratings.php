<?php
include "functions.php";
$autoload=new Functions();
$lang=$autoload->language();
include "lang_$lang.php";
$autoload->checkAutoLogin();
if($_SESSION['privilage']!='Admin'){
    header('location:index.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Makes it responsive -->
    <title>User Data</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Optional: Custom Styles -->
    <link rel="stylesheet" href="style.css">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.1.0/css/dataTables.dateTime.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"/>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light ">

<title>Datatables test</title>

<div class="container mt-5">

    <a class="btn btn-secondary" href="index.php"><?php echo BACK?></a>
    <a class="btn btn-secondary" href="<?php echo $_SESSION['previousPage'];?>"><?php echo BACK_TO_RATINGS?></a>
    <select id="tableSelect" class="form-select" style="width: 200px; display: inline-block;">
        <option selected hidden="hidden">--Choose Data--</option>
        <option value="ratings">Ratings</option>
        <option value="users">Users</option>
        <option value="products">Products</option>
        <option value="veterinarians">Veterinarians</option>
    </select><br><br>
    <table id="ratings" class="table table-striped table-bordered table-hover display" style="width:100%">
        <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Rating</th>
            <th>Email Address</th>
            <th>Rating Time</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Rating</th>
            <th>Email Address</th>
            <th>Rating Time</th>
        </tr>
        </tfoot>
    </table>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/datetime/1.1.0/js/dataTables.dateTime.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="js/scripts.js"></script>

</body>
</html>
