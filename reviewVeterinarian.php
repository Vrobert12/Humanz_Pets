<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinarian Rating</title>

    <!-- Bootstrap -->
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/css/bootstrap.min.css"
          integrity="sha384-SI27wrMjH3ZZ89r4o+fGIJtnzkAnFs3E4qz9DIYioCQ5l9Rd/7UAa8DHcaL8jkWt"
          crossorigin="anonymous">

    <!-- RateYo -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">

    <style>
        body {
            background-color: #659df7;
            font-family: 'Segoe UI', sans-serif;
        }

        .rating-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-top: 3rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .rateyo {
            margin: 1rem 0;
        }

        .result {
            font-weight: bold;
            font-size: 1.2rem;
            color: #333;
        }

        h3 {
            font-weight: 600;
            color: #007bff;
            text-align: center;
        }

        input[type="submit"] {
            width: 100%;
        }

        @media (max-width: 576px) {
            .rating-card {
                padding: 1.2rem;
            }

            h3 {
                font-size: 1.25rem;
            }

            .result {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
<div class="container">
    <div class="rating-card">
        <form action="functions.php" method="post">
            <h3>Veterinarian Rating</h3>

            <div class="rateyo"
                 id="rating"
                 data-rateyo-rating="4"
                 data-rateyo-num-stars="5"
                 data-rateyo-score="3">
            </div>

            <div class="text-center mb-3">
                <span class='result'>rating : 0</span>
            </div>

            <!-- Required hidden inputs -->
            <input type="hidden" name="reviewCode" value="<?php echo $_GET['reviewCode']; ?>">
            <input type="hidden" name="rating">
            <input type="hidden" name="action" value="rateVeterinarian">

            <input type="submit" name="add" value="Send Rating" class="btn btn-primary">
        </form>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
<script>
    $(function () {
        $(".rateyo").rateYo().on("rateyo.change", function (e, data) {
            var rating = data.rating;
            $(this).parent().find('.result').text('rating : ' + rating);
            $(this).parent().find('input[name=rating]').val(rating);
        });
    });
</script>
</body>
</html>
