<?php

include "functions.php";
$autoload = new Functions();
$lang = $autoload->language();

$pdo = $autoload->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);

$sql = "SELECT review,reviewCode, reviewTime FROM review";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll();

$valid = false;
$reviewTime = "";

// Check if the reviewCode exists and get the corresponding reviewTime
foreach ($result as $row) {
    if ($row['reviewCode'] == $_GET['reviewCode'] && $row['review']==NULL) {
        $valid = true;
        $reviewTime = $row['reviewTime'];
        break; // Stop the loop once we've found the matching reviewCode
    }
}

if (!$valid) {
    // If the reviewCode doesn't exist, redirect to index
    header('Location: index.php');
    exit();
}

// Check if the review is older than 24 hours
$reviewDate = new DateTime($reviewTime);
$currentDate = new DateTime();

// Calculate the total difference in hours
$interval = $currentDate->diff($reviewDate);
$totalHours = ($interval->days * 24) + $interval->h; // Convert days to hours and add the hours part

// If the review is older than 24 hours, redirect to index
if ($totalHours >= 71) {
    header('Location: index.php');
    exit();
}
?>
<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/css/bootstrap.min.css" integrity="sha384-SI27wrMjH3ZZ89r4o+fGIJtnzkAnFs3E4qz9DIYioCQ5l9Rd/7UAa8DHcaL8jkWt" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
</head>
<body style="background: #659df7">
<div class="container">
    <div class="row">

        <form action="functions.php" method="post">

            <div>
                <h3>Student Rating System</h3>
            </div>



            <div class="rateyo" id= "rating"
                 data-rateyo-rating="4"
                 data-rateyo-num-stars="5"
                 data-rateyo-score="3">
            </div>

            <span class='result'>0</span>
            <input type="hidden" name="reviewCode" value="<?php echo $_GET['reviewCode'];?>">
            <input type="hidden" name="rating">
            <input type="hidden" name="action" value="rateVeterinarian">
    </div>

    <div><input type="submit" name="add" value="Send Rating"> </div>

    </form>
</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>

<script>


    $(function () {
        $(".rateyo").rateYo().on("rateyo.change", function (e, data) {
            var rating = data.rating;
            $(this).parent().find('.score').text('score :'+ $(this).attr('data-rateyo-score'));
            $(this).parent().find('.result').text('rating :'+ rating);
            $(this).parent().find('input[name=rating]').val(rating); //add rating value to input field
        });
    });

</script>
</body>

</html>


